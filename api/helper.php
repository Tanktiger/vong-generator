<?php
include_once "../logger.php";
class ApiHelper {

    /**
     * @var Logger
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();

        //set to get POST values
        $rest_json = file_get_contents("php://input");
        $_POST = json_decode($rest_json, true);
    }

    public function response($data)
    {
        try {
            header("Content-type: application/json; charset=utf-8");
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // JSONs are by default dynamic data
            $json = json_encode(array(
                "success" => TRUE,
                "data" => $data
            ));

            if (FALSE === $json) {
                $this->responseError("could not generate the json", json_last_error_msg());
            }

            echo $json;
            exit();
        } catch (Exception $e) {
            $this->responseError("could not generate the json", $e->getMessage());
        }

    }

    public function responseError($message, $errorMessage = NULL)
    {
        if (NULL !== $errorMessage) {
            $this->logger->debugLog($errorMessage, "ERROR");
        }
        $this->logger->debugLog($message, "DEBUG");
        header("Content-type: application/json; charset=utf-8");
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // JSONs are by default dynamic data
        echo json_encode(array(
            "success" => FALSE,
            "error" => $message
        ));
        exit();
    }
}