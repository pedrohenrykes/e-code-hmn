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
        $dataview   = new TCombo( "dataview" );
        $datacolumn = new TCombo( "datacolumn" );
        $dashauxtxt = new TEntry( "dashauxtxt" );
        $dashtitle  = new TEntry( "dashtitle" );
        $dashicon   = new TEntry( "dashicon" );
        $bgdcolor   = new TColor( "bgdcolor" );
        $txtcolor   = new TColor( "txtcolor" );
        $dashpage   = new TCombo( "dashpage" );
        $dashaction = new TEntry( "dashaction" );

        $datacolumn->setDefaultOption( "..::SELECIONE::.." );
        $datacolumn->setEditable( false );

        $dataview->addItems( $this->getDatabaseViews() );
        $dataview->setDefaultOption( "..::SELECIONE::.." );
        $dataview->setChangeAction( new TAction( [ $this, 'onChangeComboDataColumn' ] ) );

        $dashpage->enableSearch();
        $dashpage->addItems( $this->getPageClasses() );
        $dashpage->setChangeAction( new TAction( [ $this, 'onChangeMultiSearchPage' ] ) );

        $dataview->setSize( "38%" );
        $datacolumn->setSize( "38%" );
        $dashauxtxt->setSize( "38%" );
        $dashtitle->setSize( "38%" );
        $dashicon->setSize( "38%" );
        $bgdcolor->setSize( "38%" );
        $txtcolor->setSize( "38%" );
        $dashpage->setSize( "38%" );
        $dashaction->setSize( "38%" );

        $dataview->addValidation( TextFormat::set( "View" ), new TRequiredValidator );
        $datacolumn->addValidation( TextFormat::set( "Coluna" ), new TRequiredValidator );
        $dashtitle->addValidation( TextFormat::set( "Título" ), new TRequiredValidator );
        $dashicon->addValidation( TextFormat::set( "Icone" ), new TRequiredValidator );
        $bgdcolor->addValidation( TextFormat::set( "Cor do Componente" ), new TRequiredValidator );
        $txtcolor->addValidation( TextFormat::set( "Cor do Texto" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "View: $redstar" ) ], [ $dataview ] );
        $this->form->addFields( [ new TLabel( "Coluna: $redstar" ) ], [ $datacolumn ] );
        $this->form->addFields( [ new TLabel( "Texto Auxiliar:" ) ], [ $dashauxtxt ] );
        $this->form->addFields( [ new TLabel( "Título: $redstar" ) ], [ $dashtitle ] );
        $this->form->addFields( [ new TLabel( "Icone: $redstar" ) ], [ $dashicon ] );
        $this->form->addFields( [ new TLabel( "Cor do Componente: $redstar" ) ], [ $bgdcolor ] );
        $this->form->addFields( [ new TLabel( "Cor do Texto: $redstar" ) ], [ $txtcolor ] );
        $this->form->addFields( [ new TLabel( "Página:" ) ], [ $dashpage ] );
        $this->form->addFields( [ new TLabel( "Ação:" ) ], [ $dashaction ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        TQuickForm::hideField("form_dashboard", "dashaction");

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "DashBoardModel" );

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

                if ( !empty( $object->dashaction ) ) {
                    TQuickForm::showField("form_dashboard", "dashaction");
                }

                $this->form->setData( $object );

                self::onChangeComboDataColumn( [ 'dataview' => $object->dataview ] );

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

        if ( empty( $param[ "dashpage" ] ) ) {

            $object->dashaction = "";
            TQuickForm::sendData( "form_dashboard", $object );
            TQuickForm::hideField( "form_dashboard", "dashaction" );

        } else {

            $object->dashaction = "onReload";
            TQuickForm::sendData( "form_dashboard", $object );
            TQuickForm::showField( "form_dashboard", "dashaction" );

        }
    }

    public static function onChangeComboDataColumn( $param = null )
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

            TCombo::enableField( "form_dashboard", "datacolumn" );
            TCombo::reload( "form_dashboard", "datacolumn", $columns, true );
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
