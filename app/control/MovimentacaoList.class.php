<?php
/**
 * MovimentacaoList Listing
 * @author  <your name here>
 */
class MovimentacaoList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('communication');            // defines the database
        $this->setActiveRecord('Movimentacao');   // defines the active record
        $this->setDefaultOrder('movimentacao_id', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('dt_mov', '=', 'dt_mov'); // filterField, operator, formField
        $this->addFilterField('system_unit_id', '=', 'system_unit_id'); // filterField, operator, formField

        $this->form = new TForm('form_search_Movimentacao');
        
        $dt_mov = new TDate('dt_mov');
        $system_unit_id = new TDBUniqueSearch('system_unit_id', 'communication', 'SystemUnit', 'id', 'name');

        $dt_mov->exitOnEnter();

        $dt_mov->setSize('100%');
        $system_unit_id->setSize('100%');

        $dt_mov->tabindex = -1;
        $system_unit_id->tabindex = -1;

        $dt_mov->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        $system_unit_id->setChangeAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns
        $column_dt_mov = new TDataGridColumn('dt_mov', 'Data Movimento', 'center');
        $column_system_unit_id = new TDataGridColumn('system_unit_id', 'Unidade', 'left');
        $column_valor_apurado_maq = new TDataGridColumn('valor_apurado_maq', 'Apurado Maqui', 'right');
        $column_valor_apurado_talao = new TDataGridColumn('valor_apurado_talao', 'Valor Apurado Talao', 'right');
        $column_pagamento_maq = new TDataGridColumn('pagamento_maq', 'Pagamento Maq', 'right');
        $column_pagamento_talao = new TDataGridColumn('pagamento_talao', 'Pagamento Talao', 'right');
        $column_despesas_valor = new TDataGridColumn('despesas_valor', 'Despesas Valor', 'right');
        $column_retecao = new TDataGridColumn('= {valor_apurado_maq} + {valor_apurado_talao} - {pagamento_maq} - {pagamento_talao}', 'Retecao','right');
        $column_lucro_preju    = new TDataGridColumn('= {valor_apurado_maq} + {valor_apurado_talao} - {pagamento_maq} - {pagamento_talao} - {despesas_valor}', 'Lucro Preju','right');
        //$column_lucro_preju    = new TDataGridColumn('= {valor_apurado_maq} + {valor_apurado_talao} - {pagamento_maq} + {pagamento_talao} + {despesas_valor}', 'Lucro Preju', 'right');
        //$column_lucro_preju = new TDataGridColumn('lucro_preju', 'Lucro Preju', 'right');

        $column_despesas_justificativa = new TDataGridColumn('despesas_justificativa', 'Despesas Justificativa', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_dt_mov);
        $this->datagrid->addColumn($column_system_unit_id);
        $this->datagrid->addColumn($column_valor_apurado_maq);
        $this->datagrid->addColumn($column_valor_apurado_talao);
        $this->datagrid->addColumn($column_pagamento_maq);
        $this->datagrid->addColumn($column_pagamento_talao);
        $this->datagrid->addColumn($column_despesas_valor);
        $this->datagrid->addColumn($column_retecao);
        $this->datagrid->addColumn($column_lucro_preju);
        $this->datagrid->addColumn($column_despesas_justificativa);


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

        // define the transformer method over image
        $column_despesas_valor->setTransformer( function($value, $object, $row) {
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
        
        // add datagrid inside form
        $this->form->add($this->datagrid);
        
        // create row with search inputs
        $tr = new TElement('tr');
        $this->datagrid->prependRow($tr);
        
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', ''));
        $tr->add( TElement::tag('td', $dt_mov));
        $tr->add( TElement::tag('td', $system_unit_id));

        $this->form->addField($dt_mov);
        $this->form->addField($system_unit_id);

        // keep form filled
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data'));
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('Movimentacao');
        $panel->add($this->form);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        //$panel->addHeaderActionLink( _t('New'),  new TAction(['MovimentacaoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
    }
}
