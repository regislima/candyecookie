<?php

namespace Framework\Widgets\Datagrid;

use Framework\Control\Action;

class PageNavigation
{
    private $action;
    private $pageSize;
    private $currentPage;
    private $totalRecords;

    public function __construct()
    {
        $this->pageSize = 10;
    }

    public function setAction(Action $action)
    {
        $this->action = $action;
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    public function show()
    {
        $pages = ceil($this->totalRecords / $this->pageSize);

        echo '<ul class="pagination justify-content-center">';
        for ($n = 1; $n <= $pages; $n++) {
            $offset = ($n - 1) * $this->pageSize;

            $action = $this->action;
            $action->setParameter('offset', $offset);
            $action->setParameter('page', $n);

            $url = $action->serialize();
            $class = ($this->currentPage == $n) ? 'active' : '';

            echo "<li class='page-item {$class}'>";
            echo "<a class='page-link' href='$url'>{$n}</a>";
            echo '</li>';
        }
        echo '</ul>';
    }
}
