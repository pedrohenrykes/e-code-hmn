<?php

class DashBoardCreate extends TPage
{
    private $dashboard;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->dashboard = new TElement( "div" );
        $this->dashboard->class = "row clearfix";

        $paneltitlediv = new TElement( "div" );
        $paneltitleh2 = new TElement( "h2" );
        $paneltitlediv->class = "block-header";
        $paneltitlediv->style = "margin-top: 15px";
        $paneltitlediv->add( $paneltitleh2 );
        $paneltitleh2->add( "PACIENTES CLASSIFICADOS" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $paneltitlediv );
        $container->add( $this->dashboard );

        parent::add( $container );
    }

    private function mountDashboard($param = null )
    {
        $divcolumn = new TElement( "div" );
        $divsbox   = new TElement( "div" );
        $divicon   = new TElement( "div" );
        $icon      = new TElement( "i" );
        $divinfo   = new TElement( "div" );
        $title     = new TElement( "div" );
        $number    = new TElement( "div" );
        $text      = new TElement( "div" );
        $action    = new TElement( "a" );

        $divcolumn->class = "col-lg-3 col-md-3 col-sm-6 col-xs-12";
        $divsbox->class = "info-box hover-zoom-effect";
        $divsbox->style = "color:{$param->txtcolor} !important;";
        $divicon->class = "icon";
        $divicon->style = "background-color:{$param->bgdcolor} !important;";
        $icon->class = "material-icons";
        $divinfo->class = "content";
        $title->class = "text";
        $title->style = "font-weight:bold; color:{$param->txtcolor} !important;";
        $number->class = "number";
        $number->style = "color:{$param->txtcolor} !important;";
        $text->style = "font-size:medium; display:unset;";
        $action->href = "index.php?class={$param->dashpage}&method={$param->dashaction}";

        $action->add( $divcolumn );
        $divcolumn->add( $divsbox );
        $divsbox->add( $divicon );
        $divsbox->add( $divinfo );
        $divicon->add( $icon );
        $icon->add( $param->dashicon );
        $divinfo->add( $title );
        $title->add( mb_strtoupper( $param->dashtitle, "UTF-8" ) );
        $divinfo->add( $number );
        $number->add( $param->datacolumn );
        $number->add( $text );
        $text->add( " " . mb_strtoupper( $param->dashauxtxt, "UTF-8" ) );

        $this->dashboard->add( $action );
    }

    public function onReload()
    {
        if ( !$this->loaded ) {

            try {

                TTransaction::open( "database" );

                $repository = new TRepository( "DashBoardModel" );

                $criteria = new TCriteria();
                $criteria->setProperties([
                    "order" => "id",
                    "direction" => "asc",
                ]);

                $objects = $repository->load( $criteria );

                if ( $objects ) {

                    $conn = TTransaction::get();

                    foreach ( $objects as $object ) {

                        $sql = "SELECT $object->datacolumn FROM $object->dataview";

                        $result = $conn->query( $sql, PDO::FETCH_OBJ );

                        foreach ( $result as $row ) {
                            $object->datacolumn = $row->amount;
                        }

                        $this->mountDashboard( $object );
                    }
                }

                TTransaction::close();

                $this->loaded = true;

            } catch ( Exception $ex ) {

                TTransaction::rollback();

                new TMessage( "error", $ex->getMessage() );

            }
        }
    }

    public function show()
    {
        $this->onReload();

        parent::show();
    }

}
