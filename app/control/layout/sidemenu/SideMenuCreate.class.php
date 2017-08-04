<?php

use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;

class SideMenuCreate
{
    public function __construct()
    {
        TTransaction::open( "database" );

        $repository = new TRepository( "SideMenuModel" );

        $criteria = new TCriteria();

        $objects = $repository->load( $criteria );

        if ( $objects ) {
            foreach ($objects as $object) {
                print_r($object);
                echo "<br><br>";
            }
        }

        TTransaction::close();
    }

    public static function create( $menuOptions )
    {
        ob_start();

        $callback = array('SystemPermission', 'checkPermission');

        $xml = new SimpleXMLElement(file_get_contents('menu.xml'));

        $menu = new TMenu($xml, $callback, 1, 'treeview-menu', 'treeview', '');
        $menu->class = 'sidebar-menu';
        $menu->id    = 'side-menu';
        $menu->show();

        $menu_string = ob_get_clean();

        return $menu_string;
    }
}