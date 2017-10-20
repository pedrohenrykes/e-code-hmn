<?php

class PacientesAtendimentoList extends TStandardList
{
    protected $form;
    protected $datagrid; 
    protected $pageNavigation;

    public function __construct()
    {
        parent::__construct();

        $criteria = new TCriteria();
        $criteria->add(new TFilter('situacao', '=', 'CLASSIFICADO'));

        parent::setDatabase('database');
        parent::setActiveRecord('VwBauPacientesRecord');
        parent::setCriteria($criteria);

        parent::addFilterField('nomepaciente', 'like', 'nomepaciente');
        //parent::addFilterField('dataentrada', 'like', 'nomepaciente');
        //parent::addFilterField('dataentrada', '=', 'nomepaciente'); // filter field, operator, form field
        
        parent::setDefaultOrder('dataentrada', 'desc');
        $this->setLimit(-1); // turn off limit for datagrid

        $this->form = new BootstrapFormBuilder( "list_pacientes_atendimento" );
        $this->form->setFormTitle( "Atendimento " );
        $this->form->class = "tform";

        //$opcao = new TCombo( "opcao" );
        $dados = new TEntry( "nomepaciente" );

        //$opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty ( "title", "Informe os dados referentes a opção" );

        //$opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        //$opcao->addItems( [ "nomepaciente" => "Paciente" ] );

        //$this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );

        //$this->form->setData( TSession::getValue('nomepaciente') );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome   = new TDataGridColumn( "nomepacientecor", "Classificação/Paciente", "left" );
        $column_dataentrada     = new TDataGridColumn( "dataentrada", "Data de Chegada", "left" );
        $column_horaentrada     = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );
        $column_queixaprincipal = new TDataGridColumn( "queixaprincipal", "Queixa Principal", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );
        $this->datagrid->addColumn( $column_queixaprincipal );

        $action_avaliacao = new CustomDataGridAction( [ "AtendimentoDetail", "onReload" ] );
        $action_avaliacao->setButtonClass( "btn btn-primary" );
        $action_avaliacao->setImage( "fa:user-md white fa-lg" );
        $action_avaliacao->setField( "bau_id" );
        $action_avaliacao->setFk( "bau_id" );
        $action_avaliacao->setDid( "paciente_id" );
        $action_avaliacao->setUseButton(TRUE);
        $this->datagrid->addQuickAction( "Atender", $action_avaliacao, 'bau_id');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );

        //$container->add($this->datagrid);
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function show(){

        $this->onReload();
        parent::show();

    }
}
