<?php

//use FDPF;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;

class RelatorioPrincipioAtivoMedicamentoPDF extends FPDF {

    function Header() {
        $this->Image("app/images/system/hmn.jpg", 8, 15, 28, 10);

        $this->SetFont('Arial', 'B', 12);
        $this->SetY("15");
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("RELATORIO DE MEDICAMENTOS"), 0, 1, 'L');
        $this->SetX("35");
        $this->Cell(0, 5, utf8_decode("HOSPITAL MUNICIPAL DE NATAL"), 0, 1, 'L');


        $this->Ln(6);

        $this->ColumnHeader();
    }

    function ColumnHeader() {

        $this->SetFont('Arial', 'B', 10);

        $this->Cell(25 , 5, utf8_decode("Medicamento"), 0, 0, 'L');
        $this->Cell(30 , 5, utf8_decode("Principio Ativo"), 0, 1, 'L');

        $this->Cell(0, 0, '', 1, 1, 'L');

    }

    function ColumnDetail() {
        //$situacao =  $_REQUEST['principioativo'];
        $total = 0;

        TTransaction::open('database');
        $repository = new TRepository('VwPrincipioAtivoMedicamentoRecord');

        $criteria = new TCriteria;

        /*$criteria->setProperty('order', 'dataentrada', 'asc');
        if ($situacao != 'TODOS'){
            $criteria->add(new TFilter('nomeprincipioativo', '=', $situacao ));

        }
        */
        $rows = $repository->load($criteria);

        if ($rows) {

            foreach ($rows as $row) {

                $this->SetFont('Arial', '', 8);
                $this->Cell(25, 5, utf8_decode($row->nomemedicamento), 0, 0, 'L');
                $this->Cell(30, 5, utf8_decode($row->nomeprincipioativo), 0, 1, 'L');
                $this->Cell(0, 0, '', 1, 1, 'L');

                $total++;
            }

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(40, 5, utf8_decode('Total:  ' .$total . ' Medicamentos'), 0, 1, 'L');


        }

        TTransaction::close();
    }

    function Footer() {

    }
}


$pdf = new RelatorioPrincipioAtivoMedicamentoPDF("P", "mm", "A4");
$pdf->SetTitle(utf8_decode("RELATÓRIO DE MEDICAMENTOS - HMN"));
$pdf->SetSubject(utf8_decode("RELATÓRIO DE MEDICAMENTOS - HMN"));


$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);
$pdf->ColumnDetail();
//$file = "app/reports/RelatorioMedMaisUtilizadoPDF".$_SESSION['medico_id'].".pdf";
$file = "app/reports/RelatorioPrincipioAtivoMedicamentoPDF.pdf";

//abrir pdf
$pdf->Output($file);
$pdf->openFile($file);


?>
