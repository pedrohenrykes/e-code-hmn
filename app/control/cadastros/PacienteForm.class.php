<?php

class PacienteForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_paciente" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id               = new THidden( "id" );
        $nomepaciente     = new TEntry( "nomepaciente" );
        $datanascimento   = new TDate( "datanascimento" );
        $sexo             = new TCombo( "sexo" );
        $gruposanguineo   = new TCombo( "gruposanguineo" );
        $fatorrh          = new TCombo( "fatorrh" );
        $numerosus        = new TEntry( "numerosus" );
        $numerocpf        = new TEntry( "numerocpf" );
        $numerorg         = new TEntry( "numerorg" );
        $orgaorg          = new TEntry( "orgaorg" );
        $estadocivil      = new TCombo( "estadocivil" );
        $endereco         = new TEntry( "endereco" );
        $bairro           = new TEntry( "bairro" );
        $cidade           = new TEntry( "cidade" );
        $telresidencial   = new TEntry( "telresidencial" );
        $telcelular       = new TEntry( "telcelular" );
        $telcomercial     = new TEntry( "telcomercial" );
        $nomemae          = new TEntry( "nomemae" );
        $nomepai          = new TEntry( "nomepai" );

        $uf               = new TDBCombo ( "uf", "database", "EstadosRecord", "sigla", "estado", "estado" );
        $ufrg             = new TDBCombo ( "ufrg", "database", "EstadosRecord", "sigla", "estado", "estado" );
        $grauinstrucao_id = new TDBCombo ( "grauinstrucao_id", "database", "GrauInstrucaoRecord", "id", "nomegrauinstrucao", "nomegrauinstrucao" );
        $profissao_id     = new TDBCombo ( "profissao_id", "database", "ProfissaoRecord", "id", "nomeprofissao", "nomeprofissao" );

        $nomepaciente->setSize( "38%" );
        $sexo->setSize( "38%" );
        $datanascimento->setSize( "38%" );
        $gruposanguineo->setSize( "38%" );
        $fatorrh->setSize( "38%" );
        $numerosus->setSize( "38%" );
        $numerocpf->setSize( "38%" );
        $numerorg->setSize( "38%" );
        $orgaorg->setSize( "38%" );
        $estadocivil->setSize( "38%" );
        $endereco->setSize( "38%" );
        $bairro->setSize( "38%" );
        $cidade->setSize( "38%" );
        $uf->setSize( "38%" );
        $telresidencial->setSize( "38%" );
        $telcelular->setSize( "38%" );
        $telcomercial->setSize( "38%" );
        $nomemae->setSize( "38%" );
        $nomepai->setSize( "38%" );
        $ufrg->setSize( "38%" );
        $grauinstrucao_id->setSize( "38%" );
        $profissao_id->setSize( "38%" );

        $sexo->setDefaultOption( "..::SELECIONE::.." );
        $gruposanguineo->setDefaultOption( "..::SELECIONE::.." );
        $fatorrh->setDefaultOption( "..::SELECIONE::.." );
        $estadocivil->setDefaultOption( "..::SELECIONE::.." );
        $uf->setDefaultOption( "..::SELECIONE::.." );
        $ufrg->setDefaultOption( "..::SELECIONE::.." );
        $grauinstrucao_id->setDefaultOption( "..::SELECIONE::.." );
        $profissao_id->setDefaultOption( "..::SELECIONE::.." );

        $sexo->addItems( [ "M" => "Masculino", "F" => "Feminino" ] );
        $fatorrh->addItems( [ "P" => "Positivo", "N" => "Negativo" ] );
        $estadocivil->addItems( [ "Solteiro" => "Solteiro", "Casado" => "Casado", "Divorciado" => "Divorciado", "Viuvo" => "Viuvo" ] );
        $gruposanguineo->addItems( [ "A" => "A", "B" => "B", "AB" => "AB", "O" => "O" ] );

        $cidade->setValue( "NATAL" );
        $uf->setValue( "RN" );

        $datanascimento->setMask ( "dd/mm/yyyy" );
        $datanascimento->setDatabaseMask("yyyy-mm-dd");
        $numerosus->setMask( "9!" );
        $numerocpf->setMask( "999.999.999-99" );
        $numerorg->setMask( "9!" );
        $orgaorg->setMask( "S!" );
        $telcomercial->setMask( "(99)9999-9999" );
        $telresidencial->setMask( "(99)9999-9999" );
        $telcelular->setMask( "(99)99999-9999" );

        $nomepaciente->forceUpperCase();
        $orgaorg->forceUpperCase();
        $endereco->forceUpperCase();
        $bairro->forceUpperCase();
        $cidade->forceUpperCase();
        $nomemae->forceUpperCase();
        $nomepai->forceUpperCase();

        $nomepaciente->addValidation( TextFormat::set( "Nome do Paciente" ), new TRequiredValidator );
        $sexo->addValidation( TextFormat::set( "Sexo" ), new TRequiredValidator );
        $datanascimento->addValidation( TextFormat::set( "Data de Nascimento" ), new TRequiredValidator );
        $nomemae->addValidation( TextFormat::set( "Nome da Mãe" ), new TRequiredValidator );
        $numerosus->addValidation( TextFormat::set( "Cartão SUS" ), new TRequiredValidator );

        $this->form->appendPage( "Pessoal" );

        $this->form->addFields( [ new TLabel( "Cartão SUS: {$redstar}" ) ], [ $numerosus ] );
        $this->form->addFields( [ new TLabel( "Nome do Paciente: {$redstar}" ) ], [ $nomepaciente ] );
        $this->form->addFields( [ new TLabel( "Sexo: {$redstar}" ) ], [ $sexo ] );
        $this->form->addFields( [ new TLabel( "Data de Nascimento: {$redstar}" ) ], [ $datanascimento ] );
        $this->form->addFields( [ new TLabel( "Nome da Mãe: {$redstar}" ) ], [ $nomemae ] );
        $this->form->addFields( [ new TLabel( "Nome do Pai:" ) ], [ $nomepai ] );
        $this->form->addFields( [ new TLabel( "Estado Civil:" ) ], [ $estadocivil ] );
        $this->form->addFields( [ new TLabel( "RG:" ) ], [ $numerorg ] );
        $this->form->addFields( [ new TLabel( "UF Expedidor:" ) ], [ $ufrg ] );
        $this->form->addFields( [ new TLabel( "Orgão Expedidor:" ) ], [ $orgaorg ] );
        $this->form->addFields( [ new TLabel( "CPF:" ) ], [ $numerocpf ] );
        $this->form->addFields( [ new TLabel( "Grupo Sanguineo:" ) ], [ $gruposanguineo ] );
        $this->form->addFields( [ new TLabel( "Fator Rh:" ) ], [ $fatorrh ] );
        $this->form->addFields( [ new TLabel( "Grau de Instrução:" ) ], [ $grauinstrucao_id ] );
        $this->form->addFields( [ new TLabel( "Profissão Exercida:" ) ], [ $profissao_id ] );
        $this->form->addFields( [ $id ] );

        $this->form->appendPage( "Residêncial" );

        $this->form->addFields( [ new TLabel( "Endereço:" ) ], [ $endereco ]);
        $this->form->addFields( [ new TLabel( "Bairro:" ) ], [ $bairro ] );
        $this->form->addFields( [ new TLabel( "Cidade:" ) ], [ $cidade ] );
        $this->form->addFields( [ new TLabel( "UF:" ) ], [ $uf ] );
        $this->form->addFields( [ new TLabel( "Tel. Celular:" ) ], [ $telcelular ] );
        $this->form->addFields( [ new TLabel( "Tel. Residêncial:" ) ], [ $telresidencial ] );
        $this->form->addFields( [ new TLabel( "Tel. Comercial:" ) ], [ $telcomercial ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar", new TAction( [ "PacienteList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "PacienteRecord" );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "BauDetail", "onReload" ] );
            $action->setParameter( "fk", $object->id );

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

                $object = new PacienteRecord( $param[ "key" ] );

				if ( empty( $object->cidade ) ) {
					$object->cidade = "NATAL";
				}

                $this->form->setData( $object );

                TTransaction::close();

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
