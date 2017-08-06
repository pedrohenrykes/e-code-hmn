<?php

class AgendamentoForm extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder("form_agenda" );
        $this->form->setFormTitle( "Formulário de Agendamento" );
        $this->form->class = "tform";

        TTransaction::open('database');




        /*
         $repository = new TRepository('ConvenioRecord');
                $criteria = new TCriteria();

                $criteria->add(new TFilter( , '=', 'unidadeespecialidade_id'));

                $objectSystemUSer = $repository->load($criteria);


                foreach ($objectSystemUSer as $obj) {
                    $system_user_id = $obj->id;
                }
            */



        $id                   = new THidden( "id" );
        $convenio             = new TDBCombo( "convenio_id","database","ConvenioRecord","id","nome" );
        $paciente_id          = new TDBSeekButton('paciente_id', 'database', 'form_agenda ', 'PacienteRecord', 'nomepaciente', 'paciente_id', 'nomepaciente');
        $paciente_nome        = new TEntry('nomepaciente ');

        $unidade_especialidade   = new TDBCombo( "unidadeespecialidade_id" ,"database","EspecialidadeRecord","id","nomeespecialidade");
        $horaagenda           = new TEntry( "horaagenda" );
        $horachegada          = new TEntry( "horachegada" );
        $horaatendimento      = new THidden( "horaatendimento" );


        $id->setSize( '38%' );
        $convenio->setSize( '38%' );
        $convenio->setDefaultOption('::..SELECIONE::..');
        $unidade_especialidade ->setDefaultOption('::..SELECIONE::..');
        $paciente_id->setSize('10%');
        $paciente_nome->setEditable(FALSE);
        $unidade_especialidade->setSize( '38%' );
        $horaagenda->setSize( '10%' );
        $horaagenda->setMask('hh:ii');
        $horachegada->setSize( '10%' );
        $horachegada->setMask('hh:ii');
        $horaatendimento->setSize('38%');





         $this->form->addFields( [new TLabel(('Paciente'))], [$paciente_id , $paciente_nome ] );
        $this->form->addFields( [ new TLabel( 'Convenio', "#F00" )], [ $convenio ] );
        $this->form->addFields( [ new TLabel( 'Especialidade', "#F00" )], [ $unidade_especialidade ] );
        $this->form->addFields( [ new TLabel( 'Hora' )], [ $horaagenda ] );
        $this->form->addFields( [ new TLabel( 'Hora Chegada' )], [ $horachegada ] );

        $this->form->addFields( [ $id ,$horaatendimento] );

        $this->form->addAction( 'Salvar', new TAction( [ $this, 'onSave' ] ), 'fa:save' );
        //$this->form->addAction( 'Voltar para a listagem', new TAction( [ "IesList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        // $container->add( new TXMLBreadCrumb( "menu.xml", "IesList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ 'key' ] ) ) {

                TTransaction::open( 'database' );

                $object = new AgendaRecord( $param[ 'key' ] );

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar carregar o registro para edição!<br><br>' . $ex->getMessage() );
        }
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( 'database' );

            $object = $this->form->getData( 'AgendaRecord' );

            $object->store();

            TTransaction::close();

            //$action = new TAction( [ 'IesList', 'onReload' ] );

            new TMessage( 'info', 'Registro salvo com sucesso!');

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>' . $ex->getMessage() );
        }
    }
}
