<?php

use Adianti\Registry\TSession;

/**
 * MudarFilial Form
 * @author  <your name here>
 */
class MudarFilial extends \Adianti\Control\TWindow
{
    protected $form; // form

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        //parent::setTargetContainer('adianti_right_panel');


        // creates the form
        $this->form = new BootstrapFormBuilder('form_Movimentacao');
        $this->form->setFormTitle('SELECIONE A UNIDADE');
        // create the form fields
        $system_unit_id = new TDBUniqueSearch('system_unit_id', 'communication', 'SystemUnit', 'id', 'name');
        // add the fields
        $this->form->addFields( [ new TLabel('') ], [ $system_unit_id ] );
        // set sizes
        $system_unit_id->setSize('100%');
        if (!empty($movimentacao_id))
        {
            $movimentacao_id->setEditable(FALSE);
        }
        // create the form actions
        $this->form->addAction('Mudar', new TAction([$this, 'onSave']), 'fa:save green');
        //$btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        //$btn->class = 'btn btn-sm btn-primary';
        //$this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 60%';
        $container->add($this->form);
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        $this->form->validate(); // validate form data
        $data = $this->form->getData(); // get form data as array
        TSession::setValue('userunitid', $data->system_unit_id);
        $IDUnidade = TSession::getValue('userunitid');

        //Consultar Nome da Unidade
        TTransaction::open('communication');
        $conn = TTransaction::get();
        $sth = $conn->prepare("SELECT name from  system_unit where id = '".$IDUnidade."' limit 1" );
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        $NomeUnidade = implode(",",$result);
        TSession::setValue('userunitname',$NomeUnidade);

        new TMessage('info',"Mofificado para Unidade $IDUnidade - $NomeUnidade");

        AdiantiCoreApplication::gotoPage('WelcomeView');


    }
}
