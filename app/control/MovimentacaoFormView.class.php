<?php
/**
 * MovimentacaoFormView Form
 * @author  <your name here>
 */
class MovimentacaoFormView extends TPage
{
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_Movimentacao_View');
        
        $this->form->setFormTitle('Movimentacao');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        $this->form->addHeaderActionLink( _t('Print'), new TAction([$this, 'onPrint'], ['key'=>$param['key'], 'static' => '1']), 'far:file-pdf red');
        $this->form->addHeaderActionLink( _t('Edit'), new TAction(['MovimentacaoForm', 'onEdit'], ['key'=>$param['key'], 'register_state'=>'true']), 'far:edit blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    /**
     * Show data
     */
    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('communication');
        
            $object = new Movimentacao($param['key']);
            
            $label_movimentacao_id = new TLabel('Movimentacao Id:', '#333333', '', 'B');
            $label_dt_mov = new TLabel('Dt Mov:', '#333333', '', 'B');
            $label_system_unit_id = new TLabel('System Unit Id:', '#333333', '', 'B');
            $label_valor_apurado_maq = new TLabel('Valor Apurado Maq:', '#333333', '', 'B');
            $label_valor_apurado_talao = new TLabel('Valor Apurado Talao:', '#333333', '', 'B');
            $label_pagamento_maq = new TLabel('Pagamento Maq:', '#333333', '', 'B');
            $label_pagamento_talao = new TLabel('Pagamento Talao:', '#333333', '', 'B');
            $label_retecao = new TLabel('Retecao:', '#333333', '', 'B');
            $label_lucro_preju = new TLabel('Lucro Preju:', '#333333', '', 'B');
            $label_despesas_valor = new TLabel('Despesas Valor:', '#333333', '', 'B');
            $label_despesas_justificativa = new TLabel('Despesas Justificativa:', '#333333', '', 'B');
            $label_just_edicao = new TLabel('Just Edicao:', '#333333', '', 'B');
            $label_editado = new TLabel('Editado:', '#333333', '', 'B');
            $label_system_user_id = new TLabel('System User Id:', '#333333', '', 'B');
            $label_user_edit = new TLabel('User Edit:', '#333333', '', 'B');
            $label_created_at = new TLabel('Created At:', '#333333', '', 'B');
            $label_edited_at = new TLabel('Edited At:', '#333333', '', 'B');

            $text_movimentacao_id  = new TTextDisplay($object->movimentacao_id, '#333333', '', '');
            $text_dt_mov  = new TTextDisplay($object->dt_mov, '#333333', '', '');
            $text_system_unit_id  = new TTextDisplay($object->system_unit_id, '#333333', '', '');
            $text_valor_apurado_maq  = new TTextDisplay($object->valor_apurado_maq, '#333333', '', '');
            $text_valor_apurado_talao  = new TTextDisplay($object->valor_apurado_talao, '#333333', '', '');
            $text_pagamento_maq  = new TTextDisplay($object->pagamento_maq, '#333333', '', '');
            $text_pagamento_talao  = new TTextDisplay($object->pagamento_talao, '#333333', '', '');
            $text_retecao  = new TTextDisplay($object->retecao, '#333333', '', '');
            $text_lucro_preju  = new TTextDisplay($object->lucro_preju, '#333333', '', '');
            $text_despesas_valor  = new TTextDisplay($object->despesas_valor, '#333333', '', '');
            $text_despesas_justificativa  = new TTextDisplay($object->despesas_justificativa, '#333333', '', '');
            $text_just_edicao  = new TTextDisplay($object->just_edicao, '#333333', '', '');
            $text_editado  = new TTextDisplay($object->editado, '#333333', '', '');
            $text_system_user_id  = new TTextDisplay($object->system_user_id, '#333333', '', '');
            $text_user_edit  = new TTextDisplay($object->user_edit, '#333333', '', '');
            $text_created_at  = new TTextDisplay($object->created_at, '#333333', '', '');
            $text_edited_at  = new TTextDisplay($object->edited_at, '#333333', '', '');

            $this->form->addFields([$label_movimentacao_id],[$text_movimentacao_id]);
            $this->form->addFields([$label_dt_mov],[$text_dt_mov]);
            $this->form->addFields([$label_system_unit_id],[$text_system_unit_id]);
            $this->form->addFields([$label_valor_apurado_maq],[$text_valor_apurado_maq]);
            $this->form->addFields([$label_valor_apurado_talao],[$text_valor_apurado_talao]);
            $this->form->addFields([$label_pagamento_maq],[$text_pagamento_maq]);
            $this->form->addFields([$label_pagamento_talao],[$text_pagamento_talao]);
            $this->form->addFields([$label_retecao],[$text_retecao]);
            $this->form->addFields([$label_lucro_preju],[$text_lucro_preju]);
            $this->form->addFields([$label_despesas_valor],[$text_despesas_valor]);
            $this->form->addFields([$label_despesas_justificativa],[$text_despesas_justificativa]);
            $this->form->addFields([$label_just_edicao],[$text_just_edicao]);
            $this->form->addFields([$label_editado],[$text_editado]);
            $this->form->addFields([$label_system_user_id],[$text_system_user_id]);
            $this->form->addFields([$label_user_edit],[$text_user_edit]);
            $this->form->addFields([$label_created_at],[$text_created_at]);
            $this->form->addFields([$label_edited_at],[$text_edited_at]);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Print view
     */
    public function onPrint($param)
    {
        try
        {
            $this->onEdit($param);
            
            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/Movimentacao-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
