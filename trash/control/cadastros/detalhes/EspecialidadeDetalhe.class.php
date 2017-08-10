<?php

class EspecialidadeDetalhe extends TWindow
{
    private $form;
    private $foreing;

    function __construct()
    {
        parent::__construct();
        parent::setTitle('Formulário de Especialidades');
        parent::setSize(0.600, 0.800);

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_especialidade" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $this->foreing = filter_input( INPUT_GET, "fk" );

        if ( is_null( $this->foreing ) ) {
            $this->foreing = filter_input( INPUT_GET, "key" );
        }

        $id                = new THidden( "id" );
        $nomeespecialidade = new TEntry( "nomeespecialidade" );

        try {

            TTransaction::open( "database" );

            $object = new ClinicaRecord( $this->foreing );

            TTransaction::close();

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }

        $nomeespecialidade->setProperty( "title", "O campo é obrigatório" );
        $nomeespecialidade->setSize( "38%" );

        $label02 = new RequiredTextFormat( [ "Especialidade", "#F00", "bold" ] );
        $nomeespecialidade->addValidation( $label02->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Clínica:" ) ], [ '<font size="3">' . $object->nomeclinica . '</font>'] );
        $this->form->addFields( [ new TLabel( "Especialidade: $redstar" ) ], [ $nomeespecialidade ] );
        $this->form->addFields( [ $id] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onReload = new TAction( [ $this, "onReload" ] );

        $onSave->setParameter( "fk", $this->foreing );
        $onReload->setParameter( "fk", $this->foreing );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Novo", $onReload, "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_nome = new TDataGridColumn( "nomeespecialidade", "Especialidade", "left" );

        $this->datagrid->addColumn( $column_nome );

        $action_edit = new TDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $this->foreing );
        $this->datagrid->addAction( $action_edit );

        $action_del = new TDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $this->foreing );
        $this->datagrid->addAction( $action_del );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onReload( $param = NULL )
    {
      try {

        TTransaction::open( "database" );

        $repository = new TRepository( "EspecialidadeRecord" );

        if ( empty( $param[ "order" ] ) ) {
          $param[ "order" ] = "id";
          $param[ "direction" ] = "asc";
        }

        $limit = 10;

        $criteria = new TCriteria();
        $criteria->add( new TFilter( "clinica_id", "=", $this->foreing ) );
        $criteria->setProperties( $param );
        $criteria->setProperty( "limit", $limit );

        $objects = $repository->load( $criteria, FALSE );

        $this->datagrid->clear();

        if ( !empty( $objects ) ) {
          foreach ( $objects as $object ) {
            $this->datagrid->addItem( $object );
          }
        }

        $criteria->resetProperties();
        $count = $repository->count($criteria);

        $this->pageNavigation->setCount($count);
        $this->pageNavigation->setProperties($param);
        $this->pageNavigation->setLimit($limit);

        TTransaction::close();

        $this->loaded = true;

      } catch ( Exception $ex ) {

        TTransaction::rollback();

        new TMessage( "error", $ex->getMessage() );
      }
    }

    public function onSave( $param = NULL )
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "EspecialidadeRecord" );

            $object->clinica_id = $param[ "fk" ];

            $object->store();

            TTransaction::close();

            new TMessage( "info", "Registro salvo com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }

        $this->form->clear();

        $this->onReload( $param );
    }

    public function onEdit( $param = NULL )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new EspecialidadeRecord( $param[ "key" ] );

                TTransaction::close();

                $this->form->setData( $object );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }

        $this->onReload( $param );
    }

    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) ) {

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameter( "fk", $param[ "fk" ] );
            $action1->setParameter( "key", $param[ "key" ] );

            $action2->setParameter( "fk", $param[ "fk" ] );
            $action2->setParameter( "key", $param[ "key" ] );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    function Delete( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $object = new EspecialidadeRecord( $param[ "key" ] );
            $object->delete();

            TTransaction::close();

            new TMessage("info", "Registro apagado com sucesso!");

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage("error", $ex->getMessage());
        }

        $this->onReload( $param );
    }
}
