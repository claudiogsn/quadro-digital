<?php

use Adianti\Registry\TSession;

/**
 * MovimentacaoList Listing
 * @author  <your name here>
 */
class MovimentacaoList2 extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();

        $IDUnidade = TSession::getValue('userunitid');
        $NomeUnidade = TSession::getValue('userunitname');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Movimentacao');
        $this->form->setFormTitle('Movimentacao');



        // create the form fields
        $dt_mov = new TDate('dt_mov');
        $system_unit_id = new TDBUniqueSearch('system_unit_id', 'communication', 'SystemUnit', 'id', 'name');
        $system_unit_id->enableSearch();
        //$system_unit_id = new TEntry('system_unit_id');
        //$unidade = TSession::getValue('userunitid');
        //$system_unit_id->setValue($unidade);



        // add the fields
        $this->form->addFields( [ new TLabel('Data de Movimento') ], [ $dt_mov ] );
        $this->form->addFields( [ new TLabel('Unidade') ], [ $system_unit_id ] );


        // set sizes
        $dt_mov->setSize('100%');
        //$system_unit_id->setSize('100%');




        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__ . '_filter_data') );

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['MovimentacaoEditLista', 'onEdit']), 'fa:plus green');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns
        $column_dt_mov = new TDataGridColumn('dt_mov', 'Dt Mov', 'left');
        $column_system_unit_id = new TDataGridColumn('system_unit_id', 'System Unit Id', 'right');
        $column_valor_apurado_maq = new TDataGridColumn('valor_apurado_maq', 'Valor Apurado Maq', 'right');
        $column_valor_apurado_talao = new TDataGridColumn('valor_apurado_talao', 'Valor Apurado Talao', 'right');
        $column_pagamento_maq = new TDataGridColumn('pagamento_maq', 'Pagamento Maq', 'right');
        $column_pagamento_talao = new TDataGridColumn('pagamento_talao', 'Pagamento Talao', 'right');
        $column_retecao = new TDataGridColumn('retecao', 'Retecao', 'right');
        $column_lucro_preju    = new TDataGridColumn('= ({valor_apurado_maq} + 100 + {valor_apurado_talao}) - ({pagamento_maq} + {pagamento_talao} + {despesa}))', 'Lucro Preju', 'right');
        //$column_lucro_preju = new TDataGridColumn('lucro_preju', 'Lucro Preju', 'right');
        $column_despesas_valor = new TDataGridColumn('despesas_valor', 'Despesas Valor', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_dt_mov);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_valor_apurado_maq);
        $this->datagrid->addColumn($column_valor_apurado_talao);
        $this->datagrid->addColumn($column_pagamento_maq);
        $this->datagrid->addColumn($column_pagamento_talao);
        $this->datagrid->addColumn($column_retecao);
        $this->datagrid->addColumn($column_lucro_preju);
        $this->datagrid->addColumn($column_despesas_valor);

        $column_lucro_preju->setTotalFunction( function($values) {
            return array_sum((array) $values);


        // creates the datagrid column actions
        $column_dt_mov->setAction(new TAction([$this, 'onReload']), ['order' => 'dt_mov']);

        // define the transformer method over image
        $column_dt_mov->setTransformer( function($value, $object, $row) {
            if ($value)
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
            return $value;
        });

        // define the transformer method over image
        $column_valor_apurado_maq->setTransformer( function($value, $object, $row) {
            if (is_numeric($value))
            {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });

        // define the transformer method over image
        $column_valor_apurado_talao->setTransformer( function($value, $object, $row) {
            if (is_numeric($value))
            {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });

        // define the transformer method over image
        $column_pagamento_maq->setTransformer( function($value, $object, $row) {
            if (is_numeric($value))
            {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });

        // define the transformer method over image
        $column_pagamento_talao->setTransformer( function($value, $object, $row) {
            if (is_numeric($value))
            {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });

        // define the transformer method over image
        $column_retecao->setTransformer( function($value, $object, $row) {
            if (is_numeric($value))
            {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });

        // define the transformer method over image
        $column_lucro_preju->setTransformer( function($value, $object, $row) {
            if (is_numeric($value))
            {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        });



        $action1 = new TDataGridAction(['MovimentacaoEditLista', 'onEdit'], ['movimentacao_id'=>'{movimentacao_id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['movimentacao_id'=>'{movimentacao_id}']);

        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));

        parent::add($container);
    }

    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];

            TTransaction::open('communication'); // open a transaction with database
            $object = new Movimentacao($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction

            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();

        // clear session filters
        TSession::setValue(__CLASS__.'_filter_dt_mov',   NULL);
        TSession::setValue(__CLASS__.'_filter_system_unit_id',   NULL);

        if (isset($data->dt_mov) AND ($data->dt_mov)) {
            $filter = new TFilter('dt_mov', '=', $data->dt_mov); // create the filter
            TSession::setValue(__CLASS__.'_filter_dt_mov',   $filter); // stores the filter in the session
        }


        if (isset($data->system_unit_id) AND ($data->system_unit_id)) {
            $filter = new TFilter('system_unit_id', '=', $data->system_unit_id); // create the filter
            TSession::setValue(__CLASS__.'_filter_system_unit_id',   $filter); // stores the filter in the session
        }


        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);

        $param = array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'communication'
            TTransaction::open('communication');

            // creates a repository for Movimentacao
            $repository = new TRepository('Movimentacao');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;

            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'movimentacao_id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);


            if (TSession::getValue(__CLASS__.'_filter_dt_mov')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_dt_mov')); // add the session filter
            }


            if (TSession::getValue(__CLASS__.'_filter_system_unit_id')) {
                $criteria->add(TSession::getValue(__CLASS__.'_filter_system_unit_id')); // add the session filter
            }


            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead

        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }

    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('communication'); // open a transaction with database
            $object = new Movimentacao($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
