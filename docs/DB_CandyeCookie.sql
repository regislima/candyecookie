CREATE DATABASE candy_e_cookie CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_general_ci';

USE candy_e_cookie;

CREATE TABLE estado (
    id SMALLINT PRIMARY KEY NOT NULL, 
    sigla CHAR(2) NOT NULL,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE cidade (
    id SMALLINT PRIMARY KEY NOT NULL,
    nome VARCHAR(50) NOT NULL,
    id_estado SMALLINT NOT NULL,
    FOREIGN KEY (id_estado) REFERENCES estado(id)
);

CREATE TABLE grupo (
    id SMALLINT PRIMARY KEY NOT NULL,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE fabricante (
    id SMALLINT PRIMARY KEY NOT NULL,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(50) NOT NULL unique,
    url VARCHAR(100)
);

CREATE TABLE unidade (
    id SMALLINT PRIMARY KEY NOT NULL,
    sigla CHAR(3) NOT NULL,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE tipo (
    id SMALLINT PRIMARY KEY NOT NULL,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE forma_pagamento (
    id SMALLINT PRIMARY KEY NOT NULL,
    forma VARCHAR(20) NOT NULL
);

CREATE TABLE forma_entrega (
    id SMALLINT PRIMARY KEY NOT NULL,
    forma VARCHAR(20) NOT NULL
);

CREATE TABLE venda_status_opcoes (
    id SMALLINT PRIMARY KEY NOT NULL,
    status VARCHAR(20) NOT NULL
);

CREATE TABLE config (
    id SMALLINT PRIMARY KEY NOT NULL,
    valor_frete DECIMAL(8,2) DEFAULT 0.00,
    valor_minimo_frete DECIMAL(8,2) DEFAULT 0.00
);

CREATE TABLE produto (
    id SMALLINT PRIMARY KEY NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    estoque SMALLINT NOT NULL DEFAULT 0,
    preco_custo DECIMAL(8,2) NOT NULL,
    preco_venda DECIMAL(8,2) NOT NULL,
    imagem TEXT,
    obs TEXT,
    medida varchar(10) NOT NULL,
    id_unidade SMALLINT NOT NULL,
    id_tipo SMALLINT NOT NULL,
    id_fabricante SMALLINT NOT NULL,
    FOREIGN KEY (id_unidade) REFERENCES unidade(id),
    FOREIGN KEY (id_tipo) REFERENCES tipo(id),
    FOREIGN KEY (id_fabricante) REFERENCES fabricante(id)
);

CREATE TABLE produto_imagem (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_produto SMALLINT NOT NULL,
    imagem TEXT,
    FOREIGN KEY (id_produto) REFERENCES produto(id) ON DELETE CASCADE
);

CREATE TABLE pessoa (
    id SMALLINT PRIMARY KEY NOT NULL,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(20) NOT NULL unique,
    endereco VARCHAR(100) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(200) NOT NULL unique,
    senha TEXT NOT NULL,
    nascimento DATE NOT NULL,
    data_cadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    imagem TEXT,
    credito DECIMAL(8,2) DEFAULT 0.00,
    id_grupo SMALLINT NOT NULL,
    id_cidade SMALLINT NOT NULL,
    FOREIGN KEY (id_grupo) REFERENCES grupo(id),
    FOREIGN KEY (id_cidade) REFERENCES cidade(id)
);

CREATE TABLE comentario (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_produto SMALLINT NOT NULL,
    id_pessoa SMALLINT NOT NULL,
    comentario TEXT,
    nota SMALLINT,
    FOREIGN KEY (id_produto) REFERENCES produto(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pessoa) REFERENCES pessoa(id)
);

CREATE TABLE despesa (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_empresa SMALLINT NOT NULL,
    data_despesa DATE NOT NULL,
    valor DECIMAL(8,2) NOT NULL,
    id_forma_pagamento SMALLINT NOT NULL,
    descricao VARCHAR(200) NOT NULL,
    dias_cheque SMALLINT,
    FOREIGN KEY (id_empresa) REFERENCES fabricante(id),
    FOREIGN KEY (id_forma_pagamento) REFERENCES forma_pagamento(id)
);

CREATE TABLE venda (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_cliente SMALLINT NOT NULL,
    id_forma_pagamento SMALLINT NOT NULL,
    data_venda DATE NOT NULL,
    parcelas SMALLINT NOT NULL,
    subtotal DECIMAL(8,2) NOT NULL,
    desconto DECIMAL(8,2),
    acrescimos DECIMAL(8,2),
    valor_final DECIMAL(8,2) NOT NULL,
    presente INT(1),
    FOREIGN KEY (id_cliente) REFERENCES pessoa(id),
    FOREIGN KEY (id_forma_pagamento) REFERENCES forma_pagamento(id)
);

CREATE TABLE item_venda (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_produto SMALLINT NOT NULL,
    id_venda SMALLINT NOT NULL,
    quantidade SMALLINT NOT NULL,
    preco_unitario DECIMAL(8,2) NOT NULL,
    preco_total DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (id_produto) REFERENCES produto(id),
    FOREIGN KEY (id_venda) REFERENCES venda(id) ON DELETE CASCADE
);

CREATE TABLE venda_status (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_venda SMALLINT NOT NULL,
    id_status SMALLINT NOT NULL,
    data_hora DATETIME NOT NULL,
    FOREIGN KEY (id_venda) REFERENCES venda(id) ON DELETE CASCADE,
    FOREIGN KEY (id_status) REFERENCES venda_status_opcoes(id)
);

CREATE TABLE conta (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_cliente SMALLINT NOT NULL,
    id_venda SMALLINT NOT NULL,
    dt_emissao DATE,
    dt_vencimento DATE,
    valor DECIMAL(8,2),
    paga CHAR(1),
    FOREIGN KEY (id_cliente) REFERENCES pessoa(id) ON DELETE CASCADE,
    FOREIGN KEY (id_venda) REFERENCES venda(id) ON DELETE CASCADE
);

CREATE TABLE contato (
    id SMALLINT PRIMARY KEY NOT NULL,
    id_cliente SMALLINT NOT NULL,
    assunto VARCHAR(50) NOT NULL,
    mensagem TEXT NOT NULL,
    data_hora DATETIME NOT NULL,
    lida CHAR(1) NOT NULL DEFAULT 'N',
    FOREIGN KEY (id_cliente) REFERENCES pessoa(id)
);

INSERT INTO estado VALUES (1, 'RO', 'Rond??nia');
INSERT INTO estado VALUES (2, 'AC', 'Acre');
INSERT INTO estado VALUES (3, 'AM', 'Amazonas');
INSERT INTO estado VALUES (4, 'RR', 'Roraima');
INSERT INTO estado VALUES (5, 'PA', 'Par??');
INSERT INTO estado VALUES (6, 'AP', 'Amap??');
INSERT INTO estado VALUES (7, 'TO', 'Tocantins');
INSERT INTO estado VALUES (8, 'MA', 'Maranh??o');
INSERT INTO estado VALUES (9, 'PI', 'Piau??');
INSERT INTO estado VALUES (10, 'CE', 'Cear??');
INSERT INTO estado VALUES (11, 'RN', 'Rio Grande do Norte');
INSERT INTO estado VALUES (12, 'PB', 'Para??ba');
INSERT INTO estado VALUES (13, 'PE', 'Pernambuco');
INSERT INTO estado VALUES (14, 'AL', 'Alagoas');
INSERT INTO estado VALUES (15, 'SE', 'Sergipe');
INSERT INTO estado VALUES (16, 'BA', 'Bahia');
INSERT INTO estado VALUES (17, 'MG', 'Minas Gerais');
INSERT INTO estado VALUES (18, 'ES', 'Esp??rito Santo');
INSERT INTO estado VALUES (19, 'RJ', 'Rio de Janeiro');
INSERT INTO estado VALUES (20, 'SP', 'S??o Paulo');
INSERT INTO estado VALUES (21, 'PR', 'Paran??');
INSERT INTO estado VALUES (22, 'SC', 'Santa Catarina');
INSERT INTO estado VALUES (23, 'RS', 'Rio Grande do Sul');
INSERT INTO estado VALUES (24, 'MS', 'Mato Grosso do Sul');
INSERT INTO estado VALUES (25, 'MT', 'Mato Grosso');
INSERT INTO estado VALUES (26, 'GO', 'Goi??s');
INSERT INTO estado VALUES (27, 'DF', 'Distrito Federal');


INSERT INTO cidade VALUES (1, 'Abaiara', 23);
INSERT INTO cidade VALUES (2, 'Acarape', 23);
INSERT INTO cidade VALUES (3, 'Acara??', 23);
INSERT INTO cidade VALUES (4, 'Acopiara', 23);
INSERT INTO cidade VALUES (5, 'Aiuaba', 23);
INSERT INTO cidade VALUES (6, 'Alc??ntaras', 23);
INSERT INTO cidade VALUES (7, 'Altaneira', 23);
INSERT INTO cidade VALUES (8, 'Alto Santo', 23);
INSERT INTO cidade VALUES (9, 'Amontada', 23);
INSERT INTO cidade VALUES (10, 'Antonina do Norte', 23);
INSERT INTO cidade VALUES (11, 'Apuiar??s', 23);
INSERT INTO cidade VALUES (12, 'Aquiraz', 23);
INSERT INTO cidade VALUES (13, 'Aracati', 23);
INSERT INTO cidade VALUES (14, 'Aracoiaba', 23);
INSERT INTO cidade VALUES (15, 'Ararend??', 23);
INSERT INTO cidade VALUES (16, 'Araripe', 23);
INSERT INTO cidade VALUES (17, 'Aratuba', 23);
INSERT INTO cidade VALUES (18, 'Arneiroz', 23);
INSERT INTO cidade VALUES (19, 'Assar??', 23);
INSERT INTO cidade VALUES (20, 'Aurora', 23);
INSERT INTO cidade VALUES (21, 'Baixio', 23);
INSERT INTO cidade VALUES (22, 'Banabui??', 23);
INSERT INTO cidade VALUES (23, 'Barbalha', 23);
INSERT INTO cidade VALUES (24, 'Barreira', 23);
INSERT INTO cidade VALUES (25, 'Barro', 23);
INSERT INTO cidade VALUES (26, 'Barroquinha', 23);
INSERT INTO cidade VALUES (27, 'Baturit??', 23);
INSERT INTO cidade VALUES (28, 'Beberibe', 23);
INSERT INTO cidade VALUES (29, 'Bela Cruz', 23);
INSERT INTO cidade VALUES (30, 'Boa Viagem', 23);
INSERT INTO cidade VALUES (31, 'Brejo Santo', 23);
INSERT INTO cidade VALUES (32, 'Camocim', 23);
INSERT INTO cidade VALUES (33, 'Campos Sales', 23);
INSERT INTO cidade VALUES (34, 'Canind??', 23);
INSERT INTO cidade VALUES (35, 'Capistrano', 23);
INSERT INTO cidade VALUES (36, 'Caridade', 23);
INSERT INTO cidade VALUES (37, 'Carir??', 23);
INSERT INTO cidade VALUES (38, 'Cariria??u', 23);
INSERT INTO cidade VALUES (39, 'Cari??s', 23);
INSERT INTO cidade VALUES (40, 'Carnaubal', 23);
INSERT INTO cidade VALUES (41, 'Cascavel', 23);
INSERT INTO cidade VALUES (42, 'Catarina', 23);
INSERT INTO cidade VALUES (43, 'Catunda', 23);
INSERT INTO cidade VALUES (44, 'Caucaia', 23);
INSERT INTO cidade VALUES (45, 'Cedro', 23);
INSERT INTO cidade VALUES (46, 'Chaval', 23);
INSERT INTO cidade VALUES (47, 'Chor??', 23);
INSERT INTO cidade VALUES (48, 'Chorozinho', 23);
INSERT INTO cidade VALUES (49, 'Corea??', 23);
INSERT INTO cidade VALUES (50, 'Crate??s', 23);
INSERT INTO cidade VALUES (51, 'Crato', 23);
INSERT INTO cidade VALUES (52, 'Croat??', 23);
INSERT INTO cidade VALUES (53, 'Cruz', 23);
INSERT INTO cidade VALUES (54, 'Deputado Irapuan Pinheiro', 23);
INSERT INTO cidade VALUES (55, 'Erer??', 23);
INSERT INTO cidade VALUES (56, 'Eus??bio', 23);
INSERT INTO cidade VALUES (57, 'Farias Brito', 23);
INSERT INTO cidade VALUES (58, 'Forquilha', 23);
INSERT INTO cidade VALUES (59, 'Fortaleza', 23);
INSERT INTO cidade VALUES (60, 'Fortim', 23);
INSERT INTO cidade VALUES (61, 'Frecheirinha', 23);
INSERT INTO cidade VALUES (62, 'General Sampaio', 23);
INSERT INTO cidade VALUES (63, 'Gra??a', 23);
INSERT INTO cidade VALUES (64, 'Granja', 23);
INSERT INTO cidade VALUES (65, 'Granjeiro', 23);
INSERT INTO cidade VALUES (66, 'Groa??ras', 23);
INSERT INTO cidade VALUES (67, 'Guai??ba', 23);
INSERT INTO cidade VALUES (68, 'Guaraciaba do Norte', 23);
INSERT INTO cidade VALUES (69, 'Guaramiranga', 23);
INSERT INTO cidade VALUES (70, 'Hidrol??ndia', 23);
INSERT INTO cidade VALUES (71, 'Horizonte', 23);
INSERT INTO cidade VALUES (72, 'Ibaretama', 23);
INSERT INTO cidade VALUES (73, 'Ibiapina', 23);
INSERT INTO cidade VALUES (74, 'Ibicuitinga', 23);
INSERT INTO cidade VALUES (75, 'Icapu??', 23);
INSERT INTO cidade VALUES (76, 'Ic??', 23);
INSERT INTO cidade VALUES (77, 'Iguatu', 23);
INSERT INTO cidade VALUES (78, 'Independ??ncia', 23);
INSERT INTO cidade VALUES (79, 'Ipaporanga', 23);
INSERT INTO cidade VALUES (80, 'Ipaumirim', 23);
INSERT INTO cidade VALUES (81, 'Ipu', 23);
INSERT INTO cidade VALUES (82, 'Ipueiras', 23);
INSERT INTO cidade VALUES (83, 'Iracema', 23);
INSERT INTO cidade VALUES (84, 'Irau??uba', 23);
INSERT INTO cidade VALUES (85, 'Itai??aba', 23);
INSERT INTO cidade VALUES (86, 'Itaitinga', 23);
INSERT INTO cidade VALUES (87, 'Itapaj??', 23);
INSERT INTO cidade VALUES (88, 'Itapipoca', 23);
INSERT INTO cidade VALUES (89, 'Itapi??na', 23);
INSERT INTO cidade VALUES (90, 'Itarema', 23);
INSERT INTO cidade VALUES (91, 'Itatira', 23);
INSERT INTO cidade VALUES (92, 'Jaguaretama', 23);
INSERT INTO cidade VALUES (93, 'Jaguaribara', 23);
INSERT INTO cidade VALUES (94, 'Jaguaribe', 23);
INSERT INTO cidade VALUES (95, 'Jaguaruana', 23);
INSERT INTO cidade VALUES (96, 'Jardim', 23);
INSERT INTO cidade VALUES (97, 'Jati', 23);
INSERT INTO cidade VALUES (98, 'Jijoca de Jericoacoara', 23);
INSERT INTO cidade VALUES (99, 'Juazeiro do Norte', 23);
INSERT INTO cidade VALUES (100, 'Juc??s', 23);
INSERT INTO cidade VALUES (101, 'Lavras da Mangabeira', 23);
INSERT INTO cidade VALUES (102, 'Limoeiro do Norte', 23);
INSERT INTO cidade VALUES (103, 'Madalena', 23);
INSERT INTO cidade VALUES (104, 'Maracana??', 23);
INSERT INTO cidade VALUES (105, 'Maranguape', 23);
INSERT INTO cidade VALUES (106, 'Marco', 23);
INSERT INTO cidade VALUES (107, 'Martin??pole', 23);
INSERT INTO cidade VALUES (108, 'Massap??', 23);
INSERT INTO cidade VALUES (109, 'Mauriti', 23);
INSERT INTO cidade VALUES (110, 'Meruoca', 23);
INSERT INTO cidade VALUES (111, 'Milagres', 23);
INSERT INTO cidade VALUES (112, 'Milh??', 23);
INSERT INTO cidade VALUES (113, 'Mira??ma', 23);
INSERT INTO cidade VALUES (114, 'Miss??o Velha', 23);
INSERT INTO cidade VALUES (115, 'Momba??a', 23);
INSERT INTO cidade VALUES (116, 'Monsenhor Tabosa', 23);
INSERT INTO cidade VALUES (117, 'Morada Nova', 23);
INSERT INTO cidade VALUES (118, 'Mora??jo', 23);
INSERT INTO cidade VALUES (119, 'Morrinhos', 23);
INSERT INTO cidade VALUES (120, 'Mucambo', 23);
INSERT INTO cidade VALUES (121, 'Mulungu', 23);
INSERT INTO cidade VALUES (122, 'Nova Olinda', 23);
INSERT INTO cidade VALUES (123, 'Nova Russas', 23);
INSERT INTO cidade VALUES (124, 'Novo Oriente', 23);
INSERT INTO cidade VALUES (125, 'Ocara', 23);
INSERT INTO cidade VALUES (126, 'Or??s', 23);
INSERT INTO cidade VALUES (127, 'Pacajus', 23);
INSERT INTO cidade VALUES (128, 'Pacatuba', 23);
INSERT INTO cidade VALUES (129, 'Pacoti', 23);
INSERT INTO cidade VALUES (130, 'Pacuj??', 23);
INSERT INTO cidade VALUES (131, 'Palhano', 23);
INSERT INTO cidade VALUES (132, 'Palm??cia', 23);
INSERT INTO cidade VALUES (133, 'Paracuru', 23);
INSERT INTO cidade VALUES (134, 'Paraipaba', 23);
INSERT INTO cidade VALUES (135, 'Parambu', 23);
INSERT INTO cidade VALUES (136, 'Paramoti', 23);
INSERT INTO cidade VALUES (137, 'Pedra Branca', 23);
INSERT INTO cidade VALUES (138, 'Penaforte', 23);
INSERT INTO cidade VALUES (139, 'Pentecoste', 23);
INSERT INTO cidade VALUES (140, 'Pereiro', 23);
INSERT INTO cidade VALUES (141, 'Pindoretama', 23);
INSERT INTO cidade VALUES (142, 'Piquet Carneiro', 23);
INSERT INTO cidade VALUES (143, 'Pires Ferreira', 23);
INSERT INTO cidade VALUES (144, 'Poranga', 23);
INSERT INTO cidade VALUES (145, 'Porteiras', 23);
INSERT INTO cidade VALUES (146, 'Potengi', 23);
INSERT INTO cidade VALUES (147, 'Potiretama', 23);
INSERT INTO cidade VALUES (148, 'Quiterian??polis', 23);
INSERT INTO cidade VALUES (149, 'Quixad??', 23);
INSERT INTO cidade VALUES (150, 'Quixel??', 23);
INSERT INTO cidade VALUES (151, 'Quixeramobim', 23);
INSERT INTO cidade VALUES (152, 'Quixer??', 23);
INSERT INTO cidade VALUES (153, 'Reden????o', 23);
INSERT INTO cidade VALUES (154, 'Reriutaba', 23);
INSERT INTO cidade VALUES (155, 'Russas', 23);
INSERT INTO cidade VALUES (156, 'Saboeiro', 23);
INSERT INTO cidade VALUES (157, 'Salitre', 23);
INSERT INTO cidade VALUES (158, 'Santana do Acara??', 23);
INSERT INTO cidade VALUES (159, 'Santana do Cariri', 23);
INSERT INTO cidade VALUES (160, 'Santa Quit??ria', 23);
INSERT INTO cidade VALUES (161, 'S??o Benedito', 23);
INSERT INTO cidade VALUES (162, 'S??o Gon??alo do Amarante', 23);
INSERT INTO cidade VALUES (163, 'S??o Jo??o do Jaguaribe', 23);
INSERT INTO cidade VALUES (164, 'S??o Lu??s do Curu', 23);
INSERT INTO cidade VALUES (165, 'Senador Pompeu', 23);
INSERT INTO cidade VALUES (166, 'Senador S??', 23);
INSERT INTO cidade VALUES (167, 'Sobral', 23);
INSERT INTO cidade VALUES (168, 'Solon??pole', 23);
INSERT INTO cidade VALUES (169, 'Tabuleiro do Norte', 23);
INSERT INTO cidade VALUES (170, 'Tamboril', 23);
INSERT INTO cidade VALUES (171, 'Tarrafas', 23);
INSERT INTO cidade VALUES (172, 'Tau??', 23);
INSERT INTO cidade VALUES (173, 'Teju??uoca', 23);
INSERT INTO cidade VALUES (174, 'Tiangu??', 23);
INSERT INTO cidade VALUES (175, 'Trairi', 23);
INSERT INTO cidade VALUES (176, 'Tururu', 23);
INSERT INTO cidade VALUES (177, 'Ubajara', 23);
INSERT INTO cidade VALUES (178, 'Umari', 23);
INSERT INTO cidade VALUES (179, 'Umirim', 23);
INSERT INTO cidade VALUES (180, 'Uruburetama', 23);
INSERT INTO cidade VALUES (181, 'Uruoca', 23);
INSERT INTO cidade VALUES (182, 'Varjota', 23);
INSERT INTO cidade VALUES (183, 'V??rzea Alegre', 23);
INSERT INTO cidade VALUES (184, 'Vi??osa do Cear??', 23);


INSERT INTO grupo VALUES (1, 'Administrador');
INSERT INTO grupo VALUES (2, 'Cliente');

INSERT INTO config VALUES (1, 0.00, 0.00);


INSERT INTO unidade VALUES (1, 'cm', 'Cent??metro');
INSERT INTO unidade VALUES (2, 'm', 'Metro');
INSERT INTO unidade VALUES (3, 'cm2', 'Cent??metro quadrado');
INSERT INTO unidade VALUES (4, 'm2', 'Metro quadrado');
INSERT INTO unidade VALUES (5, 'cm3', 'Cent??metro c??bico');
INSERT INTO unidade VALUES (6, 'm3', 'Metro c??bico');
INSERT INTO unidade VALUES (7, 'kg', 'Kilograma');
INSERT INTO unidade VALUES (8, 'gr', 'Grama');
INSERT INTO unidade VALUES (9, 'ml', 'Mililitro');
INSERT INTO unidade VALUES (10, 'pc', 'Pe??a');
INSERT INTO unidade VALUES (11, 'pct', 'Pacote');
INSERT INTO unidade VALUES (12, 'cx', 'Caixa');
INSERT INTO unidade VALUES (13, 'sac', 'Saco');
INSERT INTO unidade VALUES (14, 'ton', 'Tonelada');
INSERT INTO unidade VALUES (15, 'kit', 'Kit');
INSERT INTO unidade VALUES (16, 'gl', 'Gal??o');
INSERT INTO unidade VALUES (17, 'fd', 'Fardo');
INSERT INTO unidade VALUES (18, 'bl', 'Bloco');
INSERT INTO unidade VALUES (19, 'un', 'Unidade');


INSERT INTO tipo VALUES (1, 'M??quina');
INSERT INTO tipo VALUES (2, 'Acess??rio');
INSERT INTO tipo VALUES (3, 'Insumo');
INSERT INTO tipo VALUES (4, 'Componente');
INSERT INTO tipo VALUES (5, 'Suprimento');


INSERT INTO forma_pagamento VALUES (1, 'Cart??o de Cr??dito');
INSERT INTO forma_pagamento VALUES (2, 'Boleto');
INSERT INTO forma_pagamento VALUES (3, 'Creditos');
INSERT INTO forma_pagamento VALUES (4, 'Cheque');


INSERT INTO venda_status_opcoes VALUES (1, 'Recebido');
INSERT INTO venda_status_opcoes VALUES (2, 'Aprovado');
INSERT INTO venda_status_opcoes VALUES (3, 'Em Transporte');
INSERT INTO venda_status_opcoes VALUES (4, 'Entregue');
INSERT INTO venda_status_opcoes VALUES (5, 'Cancelado');
INSERT INTO venda_status_opcoes VALUES (6, 'Devolvido');

-- (id, nome, cpf, endereco, bairro, telefone, email, senha, nascimento, data_cadastro, imagem, credito, id_grupo, id_cidade)
INSERT INTO pessoa VALUES (1, 'Penelope Terry', '00000000001', 'Penelope Terry Street, 1', 'Centro', '8812345678', 'penelope@email.com', '$2a$08$Cf1f11ePArKlBJomM0F6a.8hkDBVwbjEj4M.X8f8Mif742BLRbCGO', '1986-08-31', '2020-07-25 13:43:16', 'penelope-charmosa.png', 0.00, 1, 10);