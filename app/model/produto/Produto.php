<?php
use Adianti\Database\TRecord;

class Produto extends TRecord
{
    const TABLENAME = 'produto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
}