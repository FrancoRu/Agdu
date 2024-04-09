<?php
require_once 'DB/DBManagment.php';

use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};

class AdministratorService
{

  private static $instance = null;

  private static function dbInitialize()
  {
    if (self::$instance === null) {
      self::$instance = DBManagment::getInstance();
    }
    return self::$instance;
  }

  public static function readData()
  {
    $result = self::executeQuery();

    if (!$result) {
      $_SESSION['error'] = "Error en la consulta.";
      return false;
    } else {
      return self::buildView(self::buildArgs($result));
    }
  }

  private static function buildArgs($result)
  {
    $args = [];
    $result = $result->get_result();

    while ($row = $result->fetch_assoc()) {
      // Desempaqueta los valores de la fila
      $user_id = $row['user_id'];
      $fullName = $row['fullName'];
      $CBU_beneficiary = $row['CBU_beneficiary'];
      $account_description = $row['account_description'];
      $email_beneficiary = $row['email_beneficiary'];
      $children_id = $row['children_id'];
      $name_children = $row['name_children'];
      $education_description = $row['education_description'];
      $file_name = $row['file_name'];
      $url_file = $row['url_file'];
      $amount = $row['amount'];


      // Crear o actualizar la entrada para el usuario actual
      if (!isset($args[$user_id])) {
        $args[$user_id] = [
          'user_id' => $user_id,
          'fullName' => $fullName,
          'CBU_beneficiary' => $CBU_beneficiary,
          'account_description' => $account_description,
          'email_beneficiary' => $email_beneficiary,
          'childrens' => [],
          'amount' => $amount,
        ];
      }

      // Agregar el niño actual al array de childrens
      $args[$user_id]['childrens'][] = [
        'children_id' => $children_id,
        'name_children' => ucwords($name_children),
        'education_description' => $education_description,
        'file_name' => $file_name,
        'url_file' => $url_file
      ];
    }
    $values = ['values' => array_values($args)];
    return $values;
  }

  private static function executeQuery()
  {
    self::dbInitialize();
    $query = "SELECT B.user_id,
    CONCAT(B.lastname_beneficiary, ' ', B.name_beneficiary) AS fullName,
    B.CBU_beneficiary,
    A.description AS account_description,
    B.email_beneficiary,
    C.children_id,
    C.name_children,
    C.file_name,
    C.url_file,
    E.description AS education_description,
    COUNT(C.children_id) OVER (PARTITION BY B.user_id) AS amount
FROM Beneficiary B
INNER JOIN Account A ON A.account_id = B.type_account
INNER JOIN Children C ON C.user_id = B.user_id
INNER JOIN Education E ON E.education_id = C.education_level
ORDER BY B.user_id ASC;";
    return self::$instance->query($query);
  }

  private static function buildView($args)
  {
    $counterIndex = 1;
    $body = '<div class="accordion col-10" id="accordionExample">';

    foreach ($args['values'] as $arg) {
      $body .= self::buildAccordionItem($counterIndex, $arg);
      $counterIndex++;
    }

    $body .= '</div>';
    return $body;
  }

  private static function buildAccordionItem($counterIndex, $arg)
  {
    $item = "<div class='accordion-item'>
                <div class='mb-3'>
                  <h2 class='accordion-header' id='heading{$counterIndex}'>
                    <button
                      class='accordion-button'
                      type='button'
                      data-bs-toggle='collapse'
                      data-bs-target='#collapse{$counterIndex}'
                      aria-expanded='false'
                      aria-controls='collapse{$counterIndex}'>
                      <div class='proof'>
                        <div><label>C.U.I.L: {$arg['user_id']}</label></div>
                        <div><label>{$arg['fullName']}</label></div>
                        <div><label>Hijos: {$arg['amount']}</label></div>
                      </div>
                    </button>
                  </h2>";

    $item .= self::buildAccordionBody($counterIndex, $arg['CBU_beneficiary'], $arg['account_description'], $arg['email_beneficiary'], $arg['childrens']);

    $item .= "</div></div>";
    return $item;
  }

  private static function buildAccordionBody($counterIndex, $CBU, $accountDescription, $email, $childrens)
  {
    $body = "<div
                  id='collapse{$counterIndex}'
                  class='accordion-collapse collapse'
                  aria-labelledby='heading{$counterIndex}'
                  data-bs-parent='#accordionExample'>
                    <div class='accordion-body'>
                      <div class='proof'>
                        <div><label>C.B.U/Alias: {$CBU}</label></div>
                        <div><label>Tipo de cuenta: {$accountDescription}</label></div>
                        <div><label>Email: {$email}</label></div>
                      </div>
                      <div>";

    $body .= self::buildChildrens($childrens);

    $body .= "</div></div></div>";
    return $body;
  }

  private static function buildChildrens($childrens)
  {
    $counter = 1;
    $childrenHtml = "";

    foreach ($childrens as $argChildren) {
      $childrenHtml .= "<div class='proof'>
                  <div><label>Hijo $counter: {$argChildren['name_children']}</label></div>
                  <div><label>Educación {$argChildren['education_description']}</label></div>
                  <div>
                    <label>Descargar Certificado</label>
                    <label class='download' target='{$argChildren['url_file']}' name='{$argChildren['file_name']}'>
                      <img src='resources/img/download.svg' alt='Imagen de descarga'>
                    </label>
                  </div>
                </div>";
      $counter++;
    }

    return $childrenHtml;
  }



  public static function getFile(array $args)
  {
    $result = self::getQueryFile($args);
    if ($result !== false) {
      $row = $result->get_result()->fetch_assoc();
      return $row['file_children'];
    }
    return false;
  }

  private static function getQueryFile($args)
  {
    self::dbInitialize();
    $query = "SELECT file_children FROM Children WHERE children_id = ?";
    return self::$instance->query($query, $args);
  }

  public static function getXLXS()
  {
    self::dbInitialize();

    $query = "SELECT B.user_id,
    CONCAT(B.lastname_beneficiary, ' ', B.name_beneficiary) AS fullName,
    B.CBU_beneficiary,
    A.description AS account_description,
    B.email_beneficiary,
    C.name_children,
    E.description AS education_description,
    COUNT(C.children_id) OVER (PARTITION BY B.user_id) AS amount
    FROM Beneficiary B
    INNER JOIN Account A ON A.account_id = B.type_account
    INNER JOIN Children C ON C.user_id = B.user_id
    INNER JOIN Education E ON E.education_id = C.education_level
    ORDER BY B.user_id ASC;";

    $results = self::$instance->query($query);
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Informacion de beneficiarios');
    $sheet->getStyle('A1:G1')->getFont()->setBold(true);


    $fila = 2;
    $result = $results->get_result();
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->setCellValue('A1', 'CUIL');
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->setCellValue('B1', 'Nombre de beneficiario');
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->setCellValue('C1', 'Numero de cuenta');
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->setCellValue('D1', 'Tipo de cuenta');
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->setCellValue('E1', 'Correo electronico');
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->setCellValue('F1', 'Nombre del hijo');
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue('G1', 'Nivel educativo');
    $antUserId = null;
    while ($filaBD = $result->fetch_assoc()) {
      if ($antUserId !== $filaBD['user_id']) {
        $fila++;
      }
      $sheet->setCellValue('A' . $fila, $filaBD['user_id']);
      $sheet->setCellValue('B' . $fila, $filaBD['fullName']);
      $sheet->setCellValue('C' . $fila, $filaBD['CBU_beneficiary']);
      $sheet->setCellValue('D' . $fila, $filaBD['account_description']);
      $sheet->setCellValue('E' . $fila, $filaBD['email_beneficiary']);
      $sheet->setCellValue('F' . $fila, $filaBD['name_children']);
      $sheet->setCellValue('G' . $fila, $filaBD['education_description']);
      $antUserId = $filaBD['user_id'];
      $fila++;
      // Establecer el formato para todas las celdas activas
    }
    $sheet->getStyle('A1:G' . ($fila - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1:G' . ($fila - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A1:G' . ($fila - 1))->getAlignment()->setWrapText(true); // Ajustar automáticamente el tamaño de la celda

    // Ajustar automáticamente el tamaño de las columnas según el contenido
    foreach (range('A', 'G') as $column) {
      $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Crear el objeto Writer para guardar el archivo
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="informacion_beneficiarios.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
  }
  // public static function getXLXS()
  // {
  //   self::dbInitialize();

  //   $query = "SELECT B.user_id,
  //   CONCAT(B.lastname_beneficiary, ' ', B.name_beneficiary) AS fullName,
  //   B.CBU_beneficiary,
  //   A.description AS account_description,
  //   B.email_beneficiary,
  //   C.name_children,
  //   E.description AS education_description,
  //   COUNT(C.children_id) OVER (PARTITION BY B.user_id) AS amount
  //   FROM Beneficiary B
  //   INNER JOIN Account A ON A.account_id = B.type_account
  //   INNER JOIN Children C ON C.user_id = B.user_id
  //   INNER JOIN Education E ON E.education_id = C.education_level
  //   ORDER BY B.user_id ASC;";

  //   $results = self::$instance->query($query);
  //   $spreadsheet = new Spreadsheet();
  //   $sheet = $spreadsheet->getActiveSheet();
  //   $sheet->setTitle('Informacion de beneficiarios');
  //   $fila = 1;  // Iniciar desde la fila 1
  //   $result = $results->get_result();
  //   // Iterar sobre los resultados
  //   while ($filaBD = $result->fetch_assoc()) {
  //     // Información general del beneficiario
  //     $sheet->setCellValue('A' . $fila, 'CUIL');
  //     $sheet->setCellValue('B' . $fila, $filaBD['user_id']);
  //     $sheet->setCellValue('A' . ($fila ), 'Nombre');
  //     $sheet->setCellValue('B' . ($fila ), $filaBD['fullName']);
  //     $sheet->setCellValue('A' . ($fila ), 'Cantidad de hijos');
  //     $sheet->setCellValue('B' . ($fila ), $filaBD['amount']);
  //     $sheet->setCellValue('A' . ($fila ), 'Cuenta');
  //     $sheet->setCellValue('B' . ($fila ), $filaBD['CBU_beneficiary']);
  //     $sheet->setCellValue('A' . ($fila ), 'Tipo de cuenta');
  //     $sheet->setCellValue('B' . ($fila ), $filaBD['account_description']);
  //     $sheet->setCellValue('A' . ($fila ), 'email');
  //     $sheet->setCellValue('B' . ($fila ), $filaBD['email_beneficiary']);

  //     $fila += 6;  // Aumentar el índice de fila para la siguiente sección

  //     // Información de los hijos
  //     $sheet->setCellValue('B' . $fila, 'Hijo ' . $filaBD['name_children']);
  //     $sheet->setCellValue('C' . $fila, 'Nombre hijo ' . $filaBD['name_children']);
  //     $sheet->setCellValue('D' . $fila, $filaBD['name_children']);
  //     $sheet->setCellValue('C' . ($fila ), 'Nivel educativo');
  //     $sheet->setCellValue('D' . ($fila ), $filaBD['education_description']);

  //     $fila += 2;  // Aumentar el índice de fila para el próximo beneficiario
  //   }

  //   // Crear el objeto Writer para guardar el archivo
  //   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  //   header('Content-Disposition: attachment;filename="informacion_beneficiarios.xlsx"');
  //   header('Cache-Control: max-age=0');

  //   $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
  //   $writer->save('php://output');
  //   exit;
  // }
}
