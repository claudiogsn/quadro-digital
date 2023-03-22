<?php
/**
 * Movimentacao Active Record
 * @author  Claudio Gomes
 */
class Movimentacao extends TRecord
{
    const TABLENAME = 'movimentacao';
    const PRIMARYKEY= 'movimentacao_id';
    const IDPOLICY =  'max'; // {max, serial}


    private $system_user;
    private $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('dt_mov');
        parent::addAttribute('system_unit_id');
        parent::addAttribute('valor_apurado_maq');
        parent::addAttribute('valor_apurado_talao');
        parent::addAttribute('pagamento_maq');
        parent::addAttribute('pagamento_talao');
        parent::addAttribute('retecao');
        parent::addAttribute('lucro_preju');
        parent::addAttribute('despesas_valor');
        parent::addAttribute('despesas_justificativa');
        parent::addAttribute('just_edicao');
        parent::addAttribute('editado');
        parent::addAttribute('system_user_id');
        parent::addAttribute('user_edit');
        parent::addAttribute('created_at');
        parent::addAttribute('edited_at');
    }


    /**
     * Method set_system_user
     * Sample of usage: $movimentacao->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }

    /**
     * Method get_system_user
     * Sample of usage: $movimentacao->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);

        // returns the associated object
        return $this->system_user;
    }


    /**
     * Method set_system_unit
     * Sample of usage: $movimentacao->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }

    /**
     * Method get_system_unit
     * Sample of usage: $movimentacao->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);

        // returns the associated object
        return $this->system_unit;
    }



}
