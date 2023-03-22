<?php
/**
 * MovimentacaoForm Form
 * @author  Claudio Gomes
 */
class MovimentacaoForm extends TPage
{
    protected $form; // form

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();


        // creates the form
        $this->form = new BootstrapFormBuilder('form_Movimentacao');
        $this->form->setFormTitle('Lançar Movimentação');


        // create the form fields
        $movimentacao_id = new THidden('movimentacao_id');
        $dt_mov = new TDate('dt_mov');
        $system_unit_id = TSession::getValue('userunitid');
        //$system_unit_id = new TDBUniqueSearch('system_unit_id', 'communication', 'SystemUnit', 'id', 'name');
        $valor_apurado_maq = new TNumeric('valor_apurado_maq', 2, ',', '.', true);
        $valor_apurado_talao = new TNumeric('valor_apurado_tala', 2, ',', '.', true);
        $pagamento_maq = new TNumeric('pagamento_maq', 2, ',', '.', true);
        $pagamento_talao = new TNumeric('pagamento_talao', 2, ',', '.', true);
        $retecao = new TNumeric('retecao', 2, ',', '.', true);
        $lucro_preju = new TNumeric('lucro_preju', 2, ',', '.', true);
        $despesas_valor = new TNumeric('despesas_valor', 2, ',', '.', true);
        $despesas_justificativa = new TEntry('despesas_justificativa');
        $just_edicao = new THidden('just_edicao');
        $editado = new THidden('editado');
        $system_user_id = TSession::getValue('userid');;
        $user_edit = new THidden('user_edit', 'communication', 'SystemUser', 'id', 'name');
        $created_at = new THidden('created_at');
        $edited_at = new THidden('edited_at');
        //$userid_session = TSession::getValue('userid');

        //Validations


        // add the fields
        $this->form->addFields( [ new TLabel('') ], [ $movimentacao_id ] );
        $this->form->addFields( [ new TLabel('Data Movimento') ], [ $dt_mov ] );
        $this->form->addFields( [ new THidden('system_unit_id') ], [ $system_unit_id ] );
        $this->form->addFields( [ new TLabel('Valor Apurado Maq') ], [ $valor_apurado_maq ] );
        $this->form->addFields( [ new TLabel('Valor Apurado Talao') ], [ $valor_apurado_talao ] );
        $this->form->addFields( [ new TLabel('Pagamento Maq') ], [ $pagamento_maq ] );
        $this->form->addFields( [ new TLabel('Pagamento Talao') ], [ $pagamento_talao ] );
        $this->form->addFields( [ new TLabel('Retecao') ], [ $retecao ] );
        $this->form->addFields( [ new TLabel('Lucro Preju') ], [ $lucro_preju ] );
        $this->form->addFields( [ new TLabel('Despesas Valor') ], [ $despesas_valor ] );
        $this->form->addFields( [ new TLabel('Despesas Justificativa') ], [ $despesas_justificativa ] );
        $this->form->addFields( [ new TLabel('') ], [ $just_edicao ] );
        $this->form->addFields( [ new TLabel('') ], [ $editado ] );
        $this->form->addFields( [ new THidden('system_user_id') ], [$system_user_id ] );
        $this->form->addFields( [ new TLabel('') ], [ $user_edit ] );
        $this->form->addFields( [ new TLabel('') ], [ $created_at ] );
        $this->form->addFields( [ new TLabel('') ], [ $edited_at ] );

        $dt_mov->addValidation('Data Movimento', new TRequiredValidator);
        //$system_unit_id->addValidation('Unidade', new TRequiredValidator);
        $valor_apurado_maq->addValidation('Valor Apurado Maq', new TRequiredValidator);
        $valor_apurado_talao->addValidation('Valor Apurado Talao', new TRequiredValidator);
        $pagamento_maq->addValidation('Pagamento Maq', new TRequiredValidator);
        $pagamento_talao->addValidation('Pagamento Talao', new TRequiredValidator);
        $retecao->addValidation('Retecao', new TRequiredValidator);
        $lucro_preju->addValidation('Lucro Preju', new TRequiredValidator);
        //$system_user_id->addValidation('', new TRequiredValidator);


        // set sizes
        $movimentacao_id->setSize('50%');
        $dt_mov->setSize('50%');
        //$system_unit_id->setSize('50%');
        $valor_apurado_maq->setSize('50%');
        $valor_apurado_talao->setSize('50%');
        $pagamento_maq->setSize('50%');
        $pagamento_talao->setSize('50%');
        $retecao->setSize('50%');
        $lucro_preju->setSize('50%');
        $despesas_valor->setSize('50%');
        $despesas_justificativa->setSize('50%');
        $just_edicao->setSize('50%');
        $editado->setSize('50%');
        //$system_user_id->setSize('50%');
        $user_edit->setSize('50%');
        $created_at->setSize('50%');
        $edited_at->setSize('50%');



        if (!empty($movimentacao_id))
        {
            $movimentacao_id->setEditable(FALSE);
        }

        /** samples
        $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
        $fieldX->setSize( '100%' ); // set size
         **/

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
             **/

            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $object = new Movimentacao;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object

            // get the generated movimentacao_id
            $data->movimentacao_id = $object->movimentacao_id;

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
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
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
