/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    var table = $("#example").dataTable({
        "responsive": true,
        "language": {"url": "app/resources/translator.txt"},
        "dom": "lfrtipCT",
        "tableTools": {
            "sSwfPath": "//cdn.datatables.net/tabletools/2.2.3/swf/copy_csv_xls_pdf.swf",
            "sRowSelect": "single",
            "aButtons": [
                {
                    "sExtends": "print",
                    "sButtonText": "Imprimir"
                },
                {
                    "sExtends": "copy",
                    "sButtonText": "Copiar"
                },
                {
                    "sExtends": "xls",
                    "sButtonText": "Exportar Excel"
                },
                {
                    "sExtends": "pdf",
                    "sPdfOrientation": "landscape",
                    "sPdfMessage": "EMATER-RN"
                }/*,
                 {
                 "sExtends": "collection",
                 "sButtonText": "Salvar",
                 "aButtons": ["csv", "xls", "pdf"]
                 }*/
            ]
        },
        "oColVis": {
            "buttonText": "Mostrar/Ocultar",
            "restore": "Restaurar",
            "showAll": "Mostrar Todos",
            //"sAlign": "right"
            "aiExclude": [0, 1]
        }
    });
});
