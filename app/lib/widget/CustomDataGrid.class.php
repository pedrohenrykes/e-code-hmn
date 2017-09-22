<?php

use Adianti\Widget\Wrapper\TQuickGrid;

class CustomDataGrid extends TQuickGrid
{
    public function __construct()
    {
        parent::__construct();
        parent::disableDefaultClick();
    }

    protected function prepareAction( TAction $action, $object )
    {
      	parent::prepareAction( $action, $object );

        $field  = $action->getField();

        $key = isset( $object->$field ) ? $object->$field : NULL;

        $action->setParameter( 'key', $key );

        $fieldfk = $action->getFk();

        if ( isset( $fieldfk ) ) {

            if ( !isset( $object->$fieldfk ) ) {
                throw new Exception( AdiantiCoreTranslator::translate( 'FK ^1 not exists', $field ) );
            }

            $fk = isset( $object->$fieldfk ) ? $object->$fieldfk : NULL;

            $action->setParameter( 'fk', $fk );
        }

        $fielddid = $action->getDid();

        if ( isset( $fielddid ) ) {

            if ( !isset( $object->$fielddid ) ) {
                throw new Exception( AdiantiCoreTranslator::translate( 'DID ^1 not exists', $fielddid ) );
            }

            $did = isset( $object->$fielddid ) ? $object->$fielddid : NULL;

            $action->setParameter( 'did', $did );
        }
    }


}
