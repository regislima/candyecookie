<?php

namespace Framework\Traits;

use Exception;
use Framework\Database\Criteria;
use Framework\Database\Repository;
use Framework\Database\Transaction;
use Framework\Widgets\Dialog\Message;

/**
 * Carrega registros da base de dados
 */
trait ReloadTrait
{
    function onReload() {
        try {
            Transaction::open();
            $repository = new Repository($this->activeRecord);
            $criteria = new Criteria;

            if (isset($this->filters)) {
                foreach ($this->filters as $filter) {
                    if (isset($filter[3])) {
                        $criteria->add($filter[0], $filter[1], $filter[2], $filter[3]);
                    } else {
                        $criteria->add($filter[0], $filter[1], $filter[2]);
                    }
                }
            }

            // Conta o numero de registros que satisfazem o critério
            $count = $repository->count($criteria);
            
            $criteria->setProperty('order', 'id');
            $criteria->setProperty('limit', '10');
            $criteria->setProperty('offset', isset($_GET['offset']) ? (int) $_GET['offset'] : 0);

            $objects = $repository->load($criteria);
            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $this->pagNav->setTotalRecords($count);
            $this->pagNav->setCurrentPage(isset($_GET['page']) ? (int) $_GET['page'] : 1);

            Transaction::close();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
        }
    }
}
