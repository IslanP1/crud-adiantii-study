<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Registry\TSession;
use Adianti\Database\TCriteria;
use Adianti\Widget\Dialog\TMessage;

class LoginForm extends TPage
{
    protected $form;
    public function __construct(){
        parent:: __construct();

        $this->form = new BootstrapFormBuilder('form_login');
        $this->form->setFormTitle('Login');
        
        $email = new TEntry('email');
        $senha = new TEntry('senha');

        $email->setSize('100%');
        $email->setProperty('placeholder', 'Digite seu email');
        $senha->setSize('100%');
        $senha->setProperty('placeholder', 'Digite sua senha');

        $this->form->addFields([new TLabel('Email'), $email]);
        $this->form->addFields([new TLabel('Senha'), $senha]);
        $senha->setProperty('type', 'password');

        $this->form->addAction('Entrar', new TAction([$this, 'onLogin']), 'fa:sign-in green');
        $this->form->addActionLink('Cadastrar', new TAction([$this, 'goCadastro']), 'fa:user-plus blue');

        parent::add($this->form);
    }

    public function onLogin($param){
        try {
            TTransaction::open('crudestudo');

            $repo = new TRepository('Cliente');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('email', '=', $param['email']));

            $cliente = $repo->load($criteria)[0] ?? null;

            if ($cliente && $cliente->check_senha($param['senha'])) {
                // Login bem-sucedido
                TSession::setValue('cliente_id', $cliente->id);
                new TMessage('info', 'Login bem-sucedido! Bem-vindo, ' . $cliente->nome);
                // AdiantiCoreApplication::gotoPage('ProdutoList');
            } else {
                // Login falhou
                new TMessage('error', 'Usuário ou senha inválidos');
            }

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function goCadastro($param = null)
    {
        AdiantiCoreApplication::gotoPage('ClientForm');
    }
}