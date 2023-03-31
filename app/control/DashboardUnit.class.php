<?php

use Adianti\Widget\Template\THtmlRenderer;

/**
 * SystemAdministrationDashboard
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class DashboardUnit extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        try
        {
            $html = new THtmlRenderer('app/resources/dashboard_unit.html');
            
            TTransaction::open('communication');
            $indicator1 = new THtmlRenderer('app/resources/info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/info-box.html');
            $indicator6 = new THtmlRenderer('app/resources/info-box.html');
            $indicator7 = new THtmlRenderer('app/resources/info-box.html');


            $conn = TTransaction::get();
            $valor_apurado_maq = $conn->prepare("SELECT sum(valor_apurado_maq) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $valor_apurado_maq->execute();
            $Rvalor_apurado_maq = $valor_apurado_maq->fetch();

            $valor_apurado_talao = $conn->prepare("SELECT sum(valor_apurado_talao) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $valor_apurado_talao->execute();
            $valor_apurado_talaoR = $valor_apurado_talao->fetch();

            $pagamento_maq = $conn->prepare("SELECT sum(pagamento_maq) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $pagamento_maq->execute();
            $pagamento_maqR = $pagamento_maq->fetch();

            $pagamento_talao = $conn->prepare("SELECT sum(pagamento_talao) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $pagamento_talao->execute();
            $pagamento_talaoR = $pagamento_talao->fetch();

            $despesas_valor = $conn->prepare("SELECT sum(despesas_valor) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $despesas_valor->execute();
            $despesas_valorR = $despesas_valor->fetch();

            $retencao = $conn->prepare("SELECT sum(retecao) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $retencao->execute();
            $retencaoR = $retencao->fetch();

            $lucro = $conn->prepare("SELECT (sum(retecao) - sum(despesas_valor)) as result from  movimentacao where system_unit_id = '".TSession::getValue('userunitid')."'" );
            $lucro->execute();
            $lucroR = $lucro->fetch();




            $indicator1->enableSection('main', ['title' => ('Apurado Maquina'),    'icon' => 'cash-register',       'background' => 'blue', 'value' => "R$ ".$Rvalor_apurado_maq['result'].""]);
            $indicator2->enableSection('main', ['title' => ('Apurado Talão'),   'icon' => 'money-check',      'background' => 'blue',   'value' => "R$ ".$valor_apurado_talaoR['result'].""]);
            $indicator3->enableSection('main', ['title' => ('Pagamento Maquina'),    'icon' => 'cash-register', 'background' => 'orange', 'value' => "R$ ".$pagamento_maqR['result'].""]);
            $indicator4->enableSection('main', ['title' => ('Pagmento Talão'), 'icon' => 'money-check',       'background' => 'orange',  'value' => "R$ ".$pagamento_talaoR['result'].""]);
            $indicator5->enableSection('main', ['title' => ('Total Despesas'), 'icon' => 'inbox',       'background' => 'red',  'value' => "R$ ".$despesas_valorR['result'].""]);
            $indicator6->enableSection('main', ['title' => ('Retenção'), 'icon' => 'tag',       'background' => 'blue',  'value' => "R$ ".$retencaoR['result'].""]);
            $indicator7->enableSection('main', ['title' => ('Lucro'), 'icon' => 'money-bill',       'background' => 'green',  'value' => "R$ ".$lucroR['result'].""]);

            $chart2 = new THtmlRenderer('app/resources/google_bar_chart.html');
            $data2 = [];
            $data2[] = [ 'Unidade', 'Lucro' ];

            $sqlUnit = $conn->prepare("SELECT su.name as Unidade,sum(lucro_preju) as Lucro from  movimentacao INNER JOIN system_unit su on movimentacao.system_unit_id = su.id group by system_unit_id" );
            $sqlUnit->execute();
            $resultSqlUnit = $sqlUnit->fetchAll();

            foreach ($resultSqlUnit as $row) {
                    $data2[] = [$row['Unidade'],(INT)$row['Lucro']];
                };
            $chart2->enableSection('main', ['data'   => json_encode($data2),
                                            'width'  => '100%',
                                            'height'  => '500px',
                                            'title'  => ('Lucro Por unidade'),
                                            'ytitle' => ('Unidade'),
                                            'xtitle' => ('Lucro'),
                                            'uniqid' => uniqid()]);
            
            $html->enableSection('main', ['indicator1' => $indicator1,
                                          'indicator2' => $indicator2,
                                          'indicator3' => $indicator3,
                                          'indicator4' => $indicator4,
                                          'indicator5' => $indicator5,
                                          'indicator6' => $indicator6,
                                          'indicator7' => $indicator7,
                                          'chart2'     => $chart2]);
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            
            parent::add($container);
            TTransaction::close();
        }
        catch (Exception $e)
        {
            parent::add($e->getMessage());
        }
    }
}
