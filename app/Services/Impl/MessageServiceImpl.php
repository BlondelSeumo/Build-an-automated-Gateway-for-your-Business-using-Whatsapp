<?php

namespace App\Services\Impl;

use App\Services\MessageService;

class MessageServiceImpl implements MessageService
{
    public function formatText($text): array
    {
        return ['text' => $text];
    }

    public function formatImage($url, $caption = ''): array
    {
        return ['image' => ['url' => $url], 'caption' => $caption];
    }

    // formating buttons
    public function formatButtons($text, $buttons, $urlimage = '', $footer = ''): array
    {
        $buttons = array_values($buttons);

        $valueForText = $urlimage ? 'caption' : 'text';
        $message = [
            $valueForText => $text,
            'buttons' => $buttons,
            'footer' => $footer,
            'headerType' => 1,
            // 'viewOnce' => true,
        ];
        if ($urlimage) {
            $message['image'] = ['url' => $urlimage];
        }
        return $message;
    }

    // formating templates
    public function formatTemplates($text, $buttons, $urlimage = '', $footer = ''): array
    {
        $templateButtons = [];
        $i = 1;
        foreach ($buttons as $button) {

            $type = explode('|', $button)[0] . 'Button';
            $textButton = explode('|', $button)[1];
            $urlOrNumber = explode('|', $button)[2];
            $typeIcon = explode('|', $button)[0] === 'url' ? 'url' : 'phoneNumber';
            $templateButtons[] = [
                'index' => $i,
                $type => ['displayText' => $textButton, $typeIcon => $urlOrNumber],
            ];
            $i++;
        }
        $valueForText = $urlimage ? 'caption' : 'text';
        $templateMessage = [
            $valueForText => $text,
            'footer' => $footer,
            'templateButtons' => $templateButtons,
            'viewOnce' => true,
        ];
        //add image to templateMessage if exists
        if ($urlimage) {
            $templateMessage['image'] = ['url' => $urlimage];
        }
        return $templateMessage;
    }

    public function formatLists($text, $lists, $title, $buttonText, $footer = '', $urlimage = null): array
    {
        $list = [];
        $list['title'] = $title;
        $list['rows'] = [];
        foreach ($lists as $menu) {
            $list['rows'][] = [
                'title' => $menu,
                'description' => '--', // Anda bisa mengisi deskripsi jika diperlukan
            ];
        }
        $section = [
            [

                'buttonText' => $buttonText,
                'list' => [$list]
            ]
        ];
        // Membuat list message dengan format yang diminta
        $listMessage = [
            'text' => $text,
            'footer' => $footer ?? '..',
            'buttonText' => $buttonText,
            'sections' => $section,
        ];
        if ($urlimage) {
            $listMessage['image'] = ['url' => $urlimage];
        }
        return $listMessage;
    }



    public function format($type, $data): array
    {
        switch ($type) {
            case 'text':
                $reply = $this->formatText($data->message);
                break;
            case 'image':
                $reply = $this->formatImage($data->image,  $data->caption);
                break;
            case 'button':
                $buttons = [];
                foreach ($data->button as $button) {
                    $buttons[] = $button;
                }
                $reply = $this->formatButtons($data->message, $buttons, $data->image ? $data->image : '', $data->footer ?? '');
                break;
            case 'template':
                $buttons = [];
                foreach ($data->template as $button) {
                    $buttons[] = $button;
                }
                try {
                    $reply = $this->formatTemplates(
                        $data->message,
                        $buttons,
                        $data->image ? $data->image : '',
                        $data->footer ?? ''
                    );
                } catch (\Throwable $th) {
                    throw new \Exception('Invalid button type');
                }

                break;
            case 'list':
                $reply = $this->formatLists($data->message, $data->list, $data->title, $data->buttontext, $data->footer, $data->image ?? null);

                break;
            case 'media':
                $reply = $this->formatMedia($data);
                break;
            default:
                # code...
                break;
        }

        return $reply;
    }


    private function formatMedia($data)
    {
        //Log::info('data' . json_encode($data));
        $fileName = explode('/', $data->url);
        $fileName = explode('.', end($fileName));
        $fileName = implode('.', $fileName);
        $mediadetail = [
            'type' => $data->media_type,
            'url' => $data->url,
            'caption' => $data->caption,
            //  'ppt' => $data->ptt,
            'filename' => $fileName,
            'caption' => $data->caption,
        ];

        return $mediadetail;
    }
}
