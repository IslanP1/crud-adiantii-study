<?php
use Adianti\Database\TRecord;

class Produto extends TRecord
{
    const TABLENAME = 'produto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    // OBS: 'max' é o default e usa a maior chave + 1
    // o adianti já lhe fornece os campos criados no banco de dados
    // para saber quais são, veja a tabela no seu banco de dados
}