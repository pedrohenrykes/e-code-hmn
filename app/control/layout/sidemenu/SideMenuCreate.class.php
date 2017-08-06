<?php

class SideMenuCreate
{
    public static function createUserMenu()
    {
        ob_start();

        $callback = array('SystemPermission', 'checkPermission');

        $xml = new SimpleXMLElement( self::mountRuntimeXml() );

        $menu = new TMenu($xml, $callback, 1, 'treeview-menu', 'treeview', '');
        $menu->class = 'sidebar-menu';
        $menu->id    = 'side-menu';
        $menu->show();

        $menu_string = ob_get_clean();

        return $menu_string;
    }

    private static function mountRuntimeXml()
    {
        $xml = "<menu>";

        try {

            TTransaction::open("database");

            $repository = new TRepository("SideMenuModel");

            $criteria = new TCriteria();
            $criteria->setProperty( "order", "sequence" );

            $objects = $repository->load( $criteria );

            if ($objects) {

                foreach ( $objects as $menu ) {

                    if ( $menu->menu_type == "menu" &&
                         $menu->active == "Y")
                    {

                        $xml = $xml . "<menuitem label='" . $menu->name . "'><icon>" .
                            str_replace("fa-", "fa:", $menu->icon) . "</icon><menu>";

                        foreach ( $objects as $submenu ) {

                            if ( $submenu->menu_type == "submenu" &&
                                 $submenu->menu_id == $menu->id &&
                                 $submenu->active == "Y" )
                            {

                                $xml = $xml . "<menuitem label='" . $submenu->name . "'><icon>" .
                                    str_replace("fa-", "fa:", $submenu->icon) . "</icon><action>" .
                                    $submenu->action_class . "</action></menuitem>";

                            }

                        }

                        $xml = $xml . "</menu></menuitem>";

                    }

                }

            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }

        return $xml = $xml . "</menu>";
    }

}