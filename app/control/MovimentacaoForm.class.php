<?php

use Adianti\Registry\TSession;

/**
 * MovimentacaoForm Form
 * @author  Claudio Gomes
 */
class MovimentacaoForm extends \Adianti\Control\TPage
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
        $this->form->setFormTitle('Lançar Movimentação');


        // create the form fields
        $movimentacao_id = new THidden('movimentacao_id');
        $dt_mov = new TDate('dt_mov');
        $system_unit_id = new THidden('system_unit_id');
        $valor_apurado_maq = new TNumeric('valor_apurado_maq', 2, ',', '.', true, ['autocomplete' => 'off']);
        $valor_apurado_talao = new TNumeric('valor_apurado_tala', 2, ',', '.', true, ['autocomplete' => 'off']);
        $pagamento_maq = new TNumeric('pagamento_maq', 2, ',', '.', true);
        $pagamento_talao = new TNumeric('pagamento_talao', 2, ',', '.', true);
        $retecao = new TNumeric('retecao', 2, ',', '.', true);
        $lucro_preju = new TNumeric('lucro_preju', 2, ',', '.', true);
        $despesas_valor = new TNumeric('despesas_valor', 2, ',', '.', true);
        $despesas_justificativa = new TEntry('despesas_justificativa');
        $just_edicao = new THidden('just_edicao');
        $editado = new THidden('editado');
        $system_user_id = new THidden('system_user_id');
        $user_edit = new THidden('user_edit');
        $created_at = new THidden('created_at');
        $edited_at = new THidden('edited_at');

        //Validations
        $dt_mov->setMask('dd/mm/yyyy');



        // add the fields
        $this->form->addFields( [ new TLabel('') ], [ $movimentacao_id ] );
        $this->form->addFields( [ new THidden('unidade') ], [ $system_unit_id ] );
        $this->form->addFields( [ new TLabel('Data Movimento: ') ], [ $dt_mov ]);
        $this->form->addFields( [ new TLabel('Apurado Máquina: ') ], [ $valor_apurado_maq ],[ new TLabel('Apurado Talão:') ], [ $valor_apurado_talao ] );
        $this->form->addFields( [ new TLabel('Pagamento Máquina: ') ], [ $pagamento_maq ],[ new TLabel('Pagamento Talão: ') ], [ $pagamento_talao ] );
        $this->form->addFields( [ new TLabel('Retenção:') ], [ $retecao ],[ new TLabel('Lucro: ') ], [ $lucro_preju ] );
        $this->form->addFields( [ new TLabel('Valor Despesas: ') ], [ $despesas_valor ],[ new TLabel('Despesas Justificativa: ') ], [ $despesas_justificativa ] );
        $this->form->addFields( [ new TLabel('') ], [ $just_edicao ] );
        $this->form->addFields( [ new TLabel('') ], [ $editado ] );
        $this->form->addFields( [ new THidden('user') ], [$system_user_id ] );
        $this->form->addFields( [ new TLabel('') ], [ $user_edit ] );
        $this->form->addFields( [ new TLabel('') ], [ $created_at ] );
        $this->form->addFields( [ new TLabel('') ], [ $edited_at ] );

        $dt_mov->addValidation('Data Movimento', new TRequiredValidator);
        $valor_apurado_maq->addValidation('Valor Apurado Maq', new TRequiredValidator);
        $valor_apurado_talao->addValidation('Valor Apurado Talao', new TRequiredValidator);
        $pagamento_maq->addValidation('Pagamento Maq', new TRequiredValidator);
        $pagamento_talao->addValidation('Pagamento Talao', new TRequiredValidator);
        $retecao->addValidation('Retecao', new TRequiredValidator);
        $lucro_preju->addValidation('Lucro Preju', new TRequiredValidator);

        // add valor das sessions
        $unidade = TSession::getValue('userunitid');
        $system_unit_id->setValue($unidade);
        $usuario = TSession::getValue('userid');
        $system_user_id->setValue($usuario);


        // set sizes
        $movimentacao_id->setSize('25%');
        $dt_mov->setSize('25%');

        $valor_apurado_maq->setSize('40%');
        $valor_apurado_talao->setSize('40%');
        $pagamento_maq->setSize('40%');
        $pagamento_talao->setSize('40%');
        $retecao->setSize('40%');
        $lucro_preju->setSize('40%');
        $despesas_valor->setSize('40%');
        $despesas_justificativa->setSize('40%');
        $just_edicao->setSize('40%');
        $editado->setSize('40%');
        $user_edit->setSize('40%');
        $created_at->setSize('40%');
        $edited_at->setSize('40%');

        // desativar complete
        $valor_apurado_maq->setProperty('autocomplete', 'off');
        $valor_apurado_talao->setProperty('autocomplete', 'off');
        $pagamento_maq->setProperty('autocomplete', 'off');
        $pagamento_talao->setProperty('autocomplete', 'off');
        $retecao->setProperty('autocomplete', 'off');
        $lucro_preju->setProperty('autocomplete', 'off');
        $despesas_valor->setProperty('autocomplete', 'off');
        $despesas_justificativa->setProperty('autocomplete', 'off');
        $just_edicao->setProperty('autocomplete', 'off');



        if (!empty($movimentacao_id))
        {
            $movimentacao_id->setEditable(FALSE);
        }


        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onClear']), 'fa:eraser red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);

        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('communication'); // open a transaction


            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file


            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $object = new Movimentacao;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->dt_mov = DateTime::createFromFormat('d/m/Y', $object->dt_mov)->format( 'Y-m-d' );
            $idmovimento = $object->dt_mov.$object->system_unit_id;
            $object->movimentacao_id = str_replace('-','',$idmovimento);
            $object->store(); // save the object

            // get the generated movimentacao_id
            $data->movimentacao_id = $object->movimentacao_id;

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));


        }
        catch (Exception $e) // in case of exception
        {
            //$erro = $e->getMessage();
            if (str_contains($e->getMessage(),'Duplicate entry')){
                new TMessage('warning','Já existe uma Movimentação para essa data e essa unidade, favor selecionar uma outra data ou unidade !');
                // keep form data
                // undo all pending operations
            } else {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                // keep form data
                // undo all pending operations
            }
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();

        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }

    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('communication'); // open a transaction
                $object = new Movimentacao($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            //$erro = $e->getMessage();
            if (str_contains($e->getMessage(),'Duplicate entry')){
                new TMessage('warning','Já existe uma Movimentação para essa data e essa unidade, favor selecionar uma outra data ou unidade !');
                // keep form data
                // undo all pending operations
            } else {
                new TMessage('error', $e->getMessage()); // shows the exception error message
                // keep form data
                // undo all pending operations
            }
            $this->form->setData( $this->form->getData() );
            TTransaction::rollback();

        }
    }
}
