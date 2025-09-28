<?php

use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Core\AdiantiCoreApplication;

class ProductListForm extends TPage
{
    private $form;
    private $datagrid;

    public function __construct()
    {
        parent::__construct();

        // verifica se o cliente está logado
        if (!TSession::getValue('cliente_logged')) {
            AdiantiCoreApplication::gotoPage('LoginForm');
            return;
        }

        $this->form = new BootstrapFormBuilder('form_ProductList');
        $this->form->setFormTitle('Lista de Produtos');

        // Criando datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        // Definindo colunas
        $col_id       = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_nome     = new TDataGridColumn('nome', 'Nome', 'left', '30%');
        $col_preco    = new TDataGridColumn('preco', 'Preço', 'right', '20%');

        // Adicionando colunas
        $this->datagrid->addColumn($col_id);
        $this->datagrid->addColumn($col_nome);
        $this->datagrid->addColumn($col_preco);

        // Criar o modelo da datagrid
        $this->datagrid->createModel();

        // Adicionar no form
        $this->form->addContent([$this->datagrid]);

        parent::add($this->form);
    }

    public function onReload()
    {
        try {
            TTransaction::open('crudestudo'); 

            $repository = new TRepository('Produto');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('cliente_id', '=', TSession::getValue('cliente_id')));

            $objects = $repository->load($criteria);

            $this->datagrid->clear();

            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function show()
    {
        $this->onReload();
        parent::show();
    }
}
