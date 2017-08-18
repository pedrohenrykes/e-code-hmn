<?php

class TipoAltaHospitalarForm extends TWindow
{

    private $form;

    public function __construct()
    {

        parent::__construct();
        parent::setTitle( "Cadastro de Tipo Alta" );
        parent::setSize( 0.600, 0.800 );
        
        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder('form_tipoalta');
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden('id');
        $nometipoaltahospitalar = new TEntry('nometipoaltahospitalar');
        $situacao = new TCombo('situacao');
        $situacao->setDefaultOption('::..SELECIONE..::');
        
        
        $situacao->addItems([ 'ATIVO'=>'ATIVO','INATIVO'=>'INATIVO']);
       
        
        $nometipoaltahospitalar->setProperty("title", "O campo e obrigatorio");
        $situacao ->setProperty("title", "O campo e obrigatorio");

        $nometipoaltahospitalar->setSize("38%");
        $situacao->setSize("38%");

        $this->form->addFields([new TLabel("Nome do Tipo Hospitalar: $redstar")], [$nometipoaltahospitalar]);
        $this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);

        
        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        //$this->form->addAction( "Voltar para a listagem", new TAction( [ "MedicamentoList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );

    }

    public function onSave()
    {

        try {

            $this->form->validate();

            TTransaction::open('database');
                $object = $this->form->getData('TipoAltaHospitalarRecord');
                $object->store();
            TTransaction::close();
            
           // $action = new TAction( [ "MedicamentoList", "onReload" ] );
            new TMessage("info", "Registro salvo com sucesso!");
            
        } catch (Exception $ex) {
            
            TTransaction::rollback();
            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    function onEdit($param)
    {

        try {

            if (isset($param['key'])) {

                TTransaction::open( "database" );

                $object = new TipoAltaHospitalarRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }

    }
    

}

