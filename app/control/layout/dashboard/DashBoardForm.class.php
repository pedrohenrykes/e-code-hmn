<?php

class DashBoardForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Itens do Dashboard" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_dashboard" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id         = new THidden( "id" );
        $quantifier = new TCombo( "quantifier" );
        $dataview   = new TCombo( "dataview" );
        $title      = new TEntry( "title" );
        $page       = new TMultiSearch('page');
        $action     = new TEntry( "action" );

        $icon = new TDBCombo( "icon", "database", "FontAwesomeIconsModel", "class", "unicode", "id" );
        $icon->style = "font-family:'FontAwesome',Helvetica;font-size:20px";
        $icon->setValue( "fa-500px" );

        $color = new TDBCombo( "color", "database", "AdminLteColorsModel", "class", "colorname", "id" );
        $color->setDefaultOption( "..::SELECIONE::.." );

        // $quantifier->addItems( [ "amount"  => "Quantidade", "percent" => "Percentual" ] );
        $quantifier->setDefaultOption( "..::SELECIONE::.." );
        $quantifier->setEditable( false );

        $dataview->addItems( $this->getDatabaseViews() );
        $dataview->setDefaultOption( "..::SELECIONE::.." );
        $dataview->setChangeAction( new TAction( array( $this, 'onChangeComboQuantifier' ) ) );

        $page->addItems( $this->getPageClasses() );
        $page->setMaxSize(1);
        $page->setMinLength(0);
        $page->setChangeAction( new TAction( array( $this, 'onChangeMultiSearchPage' ) ) );

        $dataview->setSize( "38%" );
        $quantifier->setSize( "38%" );
        $title->setSize( "38%" );
        $icon->setSize( "38%" );
        $color->setSize( "38%" );
        $page->setSize( "38%" );
        $action->setSize( "38%" );

        $label01 = new RequiredTextFormat( [ "View", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Indicador", "#F00", "bold" ] );
        $label03 = new RequiredTextFormat( [ "Título", "#F00", "bold" ] );
        $label04 = new RequiredTextFormat( [ "Icone", "#F00", "bold" ] );
        $label05 = new RequiredTextFormat( [ "Cor", "#F00", "bold" ] );

        $dataview->addValidation( $label01->getText(), new TRequiredValidator );
        $quantifier->addValidation( $label02->getText(), new TRequiredValidator );
        $title->addValidation( $label03->getText(), new TRequiredValidator );
        $icon->addValidation( $label04->getText(), new TRequiredValidator );
        $color->addValidation( $label05->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "View: $redstar" ) ], [ $dataview ] );
        $this->form->addFields( [ new TLabel( "Indicador: $redstar" ) ], [ $quantifier ] );
        $this->form->addFields( [ new TLabel( "Título: $redstar" ) ], [ $title ] );
        $this->form->addFields( [ new TLabel( "Icone: $redstar" ) ], [ $icon ] );
        $this->form->addFields( [ new TLabel( "Cor: $redstar" ) ], [ $color ] );
        $this->form->addFields( [ new TLabel( "Página:" ) ], [ $page ] );
        $this->form->addFields( [ new TLabel( "Ação:" ) ], [ $action ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        TQuickForm::hideField("form_dashboard", "action");

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "DashBoardModel" );

            $object->page = reset( $object->page );

            $object->store();

            TTransaction::close();

            TWindow::closeWindow();

            $action = new TAction( [ "DashBoardList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }


    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new DashBoardModel( $param[ "key" ] );

                if ( !isset( $object->page ) ) {
                    unset( $object->page );
                } else {
                    $object->page = [ $object->page, $object->page ];
                }

                if ( isset( $object->action ) ) {
                    TQuickForm::showField("form_dashboard", "action");
                }

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }

    public static function onChangeMultiSearchPage( $param = null )
    {
        $object = new StdClass;

        if ( empty( reset( $param[ "page" ] ) ) ) {

            $object->action = "";
            TQuickForm::sendData( "form_dashboard", $object );
            TQuickForm::hideField( "form_dashboard", "action" );

        } else {

            $object->action = "onReload";
            TQuickForm::sendData( "form_dashboard", $object );
            TQuickForm::showField( "form_dashboard", "action" );

        }
    }

    public static function onChangeComboQuantifier( $param = null )
    {
        $columns = [];

        if ( isset( $param[ "dataview" ] ) ) {

            try {

                TTransaction::open( "database" );

                $conn = TTransaction::get();

                $stm = $conn->prepare("
                    SELECT COLUMNS.COLUMN_NAME viewcolumns
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE COLUMNS.TABLE_NAME = ?;
                ");

                $stm->execute( [ $param[ "dataview" ] ] );

                $result = $stm->fetchAll( PDO::FETCH_CLASS );

                foreach ( $result as $row ) {
                    $columns[ $row->viewcolumns ] = $row->viewcolumns;
                }

                TTransaction::close();

            } catch ( Exception $ex ) {

                TTransaction::rollback();

                new TMessage( "error", $ex->getMessage() );

            }

            TCombo::enableField( "form_dashboard", "quantifier" );
            TCombo::reload( "form_dashboard", "quantifier", $columns, true );
        }

    }

    private function getDatabaseViews()
    {
        $views = [];

        try {

            TTransaction::open( "database" );

            $conn = TTransaction::get();

            $stm = $conn->prepare("
                SELECT VIEWS.TABLE_NAME view_name
                FROM INFORMATION_SCHEMA.VIEWS
                WHERE VIEWS.TABLE_SCHEMA = ?;
            ");

            $stm->execute( [ TTransaction::getDatabaseInfo()[ "name" ] ] );

            $result = $stm->fetchAll( PDO::FETCH_CLASS );

            foreach ( $result as $row ) {
                $views[ $row->view_name ] = $row->view_name;
            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }

        return $views;
    }

    private function getPageClasses()
    {
        $entries = [];

        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "app/control" ),
            RecursiveIteratorIterator::CHILD_FIRST) as $arquivo )
        {

            if ( substr( $arquivo, -4 ) == ".php" ) {

                $name = $arquivo->getFileName();

                $pieces = explode( '.', $name );

                $class = (string) $pieces[ 0 ];

                $entries[ $class ] = $class;

            }

        }

        ksort( $entries );

        return $entries;
    }
}
