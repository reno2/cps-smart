<?php

namespace Cps\Smart;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];

    /**
     * @param int $code
     * @return self
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return self
     */
    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        header("$name: $value");
        return $this;
    }

    /**
     * @param array $data
     * @return void
     */
    public function sendJson(array $data): void
    {
        $this->addHeader('Content-Type', 'application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * @param string $content
     * @return void
     */
    public function sendText(string $content): void
    {
        $this->addHeader('Content-Type', 'text/plain');
        echo $content;
        exit;
    }

    /**
     * @param string $html
     * @return void
     */
    public function sendHtml(string $html): void
    {
        $this->addHeader('Content-Type', 'text/html');
        echo $html;
        exit;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @return void
     */
    public function sendFile(string $filePath, string $fileName): void
    {
        if (file_exists($filePath)) {
            $this->addHeader('Content-Description', 'File Transfer');
            $this->addHeader('Content-Type', 'application/octet-stream');
            $this->addHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $this->addHeader('Expires', '0');
            $this->addHeader('Cache-Control', 'must-revalidate');
            $this->addHeader('Pragma', 'public');
            $this->addHeader('Content-Length', (string)filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            $this->setStatusCode(404)->sendText("File not found.");
        }
    }
}