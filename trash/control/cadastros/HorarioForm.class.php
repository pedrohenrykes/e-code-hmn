<?php

class HorarioForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "form_horario" );
        $this->form->setFormTitle( "Formulário de horarios" );
        $this->form->class = "tform";

        $id  = new THidden( "id" );
        $horainicio = new TEntry( "horainicio" );
        $horainicio->setProperty('type', 'time');
        $horafim = new TEntry( "horafim" );
        $horafim->setProperty('type', 'time');


        $horainicio->setProperty( "title", "O campo é obrigatório" );
        $horafim->setProperty( "title", "O campo é obrigatório" );

        $horainicio->setSize( "30%" );
        $horafim->setSize( "30%" );

        $this->form->addFields( [ new TLabel( "Horario de Inicio:", "#F00" ) ], [ $horainicio ] );
        $this->form->addFields( [ new TLabel( "Horario Fim:", "#F00" ) ], [ $horafim ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "HorarioList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        // $container->add( new TXMLBreadCrumb( "menu.xml", "ProcedimentoList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "HorarioRecord" );

            $horainicio = strtotime($object->horainicio);
            $horafim = strtotime($object->horafim);

            if ($horainicio >= $horafim) {
                new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro! Horarios Incompatíveis "
                            );
            } else {
                $object->store();
                $action = new TAction( [ "HorarioList", "onReload" ] );
                new TMessage( "info", "Registro salvo com sucesso!", $action);
            }

            TTransaction::close();
        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!
            Os campos São Obrigatórios. <br><br><br><br>" );
        }
    }

    public function onEdit( $param )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new HorarioRecord( $param[ "key" ] );

                $object->nascimento = TDate::date2br( $object->nascimento );

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
