<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class ProductForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        // verifica se o cliente está logado
        if (!TSession::getValue('cliente_logged')) {
            // redireciona direto para evitar modal/loop duplicado
            AdiantiCoreApplication::gotoPage('LoginForm');
            return;
        }

        // criando o form
        $this->form = new BootstrapFormBuilder('form_Product');
        $this->form->setFormTitle('Product Form');

        // criando os campos
        $nome = new TEntry('nome');
        $preco = new TEntry('preco');

        $nome->setSize('100%');
        $nome->setProperty('placeholder', 'Digite o nome do produto');
        $preco->setSize('100%');
        $preco->setProperty('placeholder', 'Digite o preço do produto');

        // adicionando os campos ao form
        $this->form->addFields([new TLabel('Nome'), $nome]);
        $this->form->addFields([new TLabel('Preço'), $preco]);

        // botões
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        // $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');

        parent::add($this->form);
    }

    public function onSave($param)
    {
        try {
            // Validações básicas
            if (empty($param['nome'])) {
                throw new Exception('Nome do produto é obrigatório');
            }
            if (empty($param['preco'])) {
                throw new Exception('Preço do produto é obrigatório');
            }

            TTransaction::open('crudestudo');

            // salvando o produto
            $produto = new Produto;
            $produto->nome = $param['nome'];
            $produto->preco = $param['preco'];
            $produto->cliente_id = TSession::getValue('cliente_id');
            $produto->store();

            new TMessage('info', 'Produto salvo com sucesso!');
            $this->form->clear();

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function goLogin($param=null)
    {
        try {  
            AdiantiCoreApplication::gotoPage('LoginForm');
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}