<?php

namespace Cps\Smart;

class SmartAI {
    private array $payload = [];
    private string $api_key = '';
    private string $api_url = '';

    /**
     * @param $payload
     * */
    public function __construct($payload)
    {
        $this->payload = json_decode(html_entity_decode($payload), true);
        $this->api_key = getenv('CHATGPT_API_KEY');
        $this->api_url = getenv('CHATGPT_API_URL');
    }

    /**
     * @return array
     * */
    public function getSmartTitle(): array
    {
        $response_data = $this->getResult();
        $response = explode('%d%', $response_data['choices'][0]['message']['content']);
        return [
            'smart_title' => $response[0],
            'recommendation' => $response[1]
        ];
    }

    private function getResult()
    {
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->prepareHeaders());
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->prepareBody()));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * @return array
     * */
    private function prepareHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
        ];
    }

    /**
     * @return array
     * */
    private function prepareBody(): array
    {
        return [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.'
                ],
                [
                    'role' => 'user',
                    'content' => '
                Сформируй название задачи по методологии SMART для следующей задачи:
                Название задачи: "Формирование выгрузки по примененному фильтру в файл формата xlsx".
                Описание задачи: "При нажатии кнопки "Экспорт в файл" будет происходить выгрузка выборки в файл формата Excel, с учетом установленных фильтров и сортировок"
                Проект: "Кабинет ГД Томск".
                Верни следующее:
                {Только название задачи по SMART без вводного слова} {символ %d%} {Почему она не попадает под SMART, распиши критерии, но не пиши про Time Bound}
                '
                ],
                [
                    'role' => 'assistant',
                    'content' => 'КР Томск: Реализация функционала выгрузки списка подробного чек-листа
             загруженных отчетов с учетом фильтров и сортировок в xlsx-файл в административном модуле. %d% Это название уточняет, что нужно сделать, как измерить успех (функционал выгрузки данных) и для какого проекта это делается.'
                ],
                [
                    'role' => 'user',
                    'content' => '
                        Сформируй название задачи по методологии SMART для следующей задачи:
                        Название задачи: "'.$this->payload["title"].'".
                        Описание задачи: "'.$this->payload["description"].'"
                        Проект: "'.$this->payload["project"].'".
                        Верни следующее:
                        {Только название задачи по SMART без вводного слова} {символ %d%} {Почему она не попадает под SMART, распиши критерии, но не пиши про Time Bound}
                    '
                ]
            ],
            'temperature' => 0.8
        ];
    }
}