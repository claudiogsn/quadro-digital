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
        
        $this->setDatabase('table');            // defines the database
        $this->setActiveRecord('Movimentacao');   // defines the active record
        $this->setDefaultOrder('idmovimentacao', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('data_mov', '=', 'data_mov'); // filterField, operator, formField
        

        $this->form = new TForm('form_search_Movimentacao');
        
        $data_mov = new TDate('data_mov');
        

        $data_mov->exitOnEnter();

        $data_mov->setSize('100%');
 
        $data_mov->tabindex = -1;

        $data_mov->setExitAction( new TAction([$this, 'onSearch'], ['static'=>'1']) );
       
        
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_data_mov = new TDataGridColumn('data_mov', 'Data Mov', 'left');
        $column_valor_apurado_maq = new TDataGridColumn('valor_apurado_maq', 'Valor Apurado Maq', 'right');
        $column_valor_apurado_talao = new TDataGridColumn('valor_apurado_talao', 'Valor Apurado Talao', 'right');
        $column_pagamento_maq = new TDataGridColumn('pagamento_maq', 'Pagamento Maq', 'right');
        $column_pagamento_talao = new TDataGridColumn('pagamento_talao', 'Pagamento Talao', 'right');
        $column_despesas_valor = new TDataGridColumn('despesas_valor', 'Despesas Valor', 'right');
        $column_lucro_preju = new TDataGridColumn('lucro_preju', 'Lucro Preju', 'right');
        $column_retecao = new TDataGridColumn('retecao', 'Retecao', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_data_mov);
        $this->datagrid->addColumn($column_valor_apurado_maq);
        $this->datagrid->addColumn($column_valor_apurado_talao);
        $this->datagrid->addColumn($column_pagamento_maq);
        $this->datagrid->addColumn($column_pagamento_talao);
        $this->datagrid->addColumn($column_despesas_valor);
        $this->datagrid->addColumn($column_lucro_preju);
        $this->datagrid->addColumn($column_retecao);

        
        $action1 = new TDataGridAction(['MovimentacaoForm', 'onEdit'], ['idmovimentacao'=>'{idmovimentacao}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['idmovimentacao'=>'{idmovimentacao}']);
        
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
        $tr->add( TElement::tag('td', $data_mov));

        $this->form->addField($data_mov);

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
        
        $panel->addHeaderActionLink( _t('New'),  new TAction(['MovimentacaoForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green' );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($panel);
        
        parent::add($container);
    }
}
