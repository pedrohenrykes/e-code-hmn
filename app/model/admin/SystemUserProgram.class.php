<?php
/**
 * System_user_program Active Record
 * @author  <your-name-here>
 */
class SystemUserProgram extends TRecord
{
    const TABLENAME = 'usuarios_programas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}


    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_user_id');
        parent::addAttribute('system_program_id');
    }
}
?>
