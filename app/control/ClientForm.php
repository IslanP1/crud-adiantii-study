<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Database\TTransaction;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class ClientForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        // criando o form
        $this->form = new BootstrapFormBuilder('form_Client');
        $this->form->setFormTitle('Client Form');

        // criando os campos
        $nome = new TEntry('nome');
        $email = new TEntry('email');
        $senha = new TEntry('senha');
        $confirmar_senha = new TEntry('confirmar_senha');

        $nome->setSize('100%');
        $nome->setProperty('placeholder', 'Digite seu nome');
        $email->setSize('100%');
        $email->setProperty('placeholder', 'Digite seu email');
        $senha->setSize('100%');
        $senha->setProperty('placeholder', 'Digite sua senha');
        $confirmar_senha->setSize('100%');
        $confirmar_senha->setProperty('placeholder', 'Confirme sua senha');
        $senha->setProperty('type', 'password');
        $confirmar_senha->setProperty('type', 'password');

        // adicionando os campos ao form
        $this->form->addFields([new TLabel('Nome'), $nome]);
        $this->form->addFields([new TLabel('Email'), $email]);
        $this->form->addFields([new TLabel('Senha'), $senha]);
        $this->form->addFields([new TLabel('Confirmar Senha'), $confirmar_senha]);

        // botões
        $this->form->addAction('Cadastrar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addActionLink('Login', new TAction([$this, 'goLogin']), 'fa:sign-in blue');

        parent::add($this->form);
    }

    public function onSave($param)
    {
        try {
            // Validações básicas
            if (empty($param['nome'])) {
                throw new Exception('Nome é obrigatório');
            }
            if (empty($param['email'])) {
                throw new Exception('Email é obrigatório');
            }
            if (empty($param['senha'])) {
                throw new Exception('Senha é obrigatória');
            }
            if ($param['senha'] !== $param['confirmar_senha']) {
                throw new Exception('Senhas não conferem');
            }

            TTransaction::open('crudestudo');

            // Filtrar apenas os campos necessários
            $data = [];
            $data['nome'] = $param['nome'];
            $data['email'] = $param['email'];

            // comparando com js
            // let array = [], ['nome': $param['nome'], 'email': $param['email']]

            $cliente = new Cliente;
            $cliente->fromArray($data);
            $cliente->set_senha($param['senha']);
            $cliente->store();

            new TMessage('info', 'Cliente cadastrado com sucesso!');
            // $this->form->clear();
            
            TTransaction::close();
            
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onClear($param = null)
    {
        $this->form->clear();
    }

    public static function goLogin($param = null)
    {
        AdiantiCoreApplication::gotoPage('LoginFormClient');
    }
}
