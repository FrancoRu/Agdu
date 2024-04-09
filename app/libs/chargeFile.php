<?php

function loadView($viewPath, $status)
{
  if (file_exists($viewPath)) {
    ob_start();
    include $viewPath;
    $htmlContent = ob_get_clean();
    echo json_encode(array(
      'html' => $htmlContent,
      'status' => $status
    ));
    exit();
  } else {
    echo json_encode(array(
      'error' => "File not found: $viewPath",
      'status' => 404
    ));
    exit();
  }
}
