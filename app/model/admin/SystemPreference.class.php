<?php
/**
 * SystemPreference
 * @author  <your-name-here>
 */
class SystemPreference extends TRecord
{
    const TABLENAME  = 'preferencias';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('value');
    }

    /**
     * Retorna uma preferência
     * @param $id Id da preferência
     */
    public static function getPreference($id)
    {
        $preference = new SystemPreference($id);
        return $preference->value;
    }

    /**
     * Altera uma preferência
     * @param $id  Id da preferência
     * @param $value Valor da preferência
     */
    public static function setPreference($id, $value)
    {
        $preference = SystemPreference::find($id);
        if ($preference)
        {
            $preference->value = $value;
            $preference->store();
        }
    }

    /**
     * Retorna um array com todas preferências
     */
    public static function getAllPreferences()
    {
        $rep = new TRepository('SystemPreference');
        $objects = $rep->load(new TCriteria);
        $dataset = array();

        if ($objects)
        {
            foreach ($objects as $object)
            {
                $property = $object->id;
                $value    = $object->value;
                $dataset[$property] = $value;
            }
        }
        return $dataset;
    }
}
