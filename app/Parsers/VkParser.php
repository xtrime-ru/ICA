<?php


namespace App\Parsers;


use App\Models\Post;

class VkParser extends BaseParser
{
    protected function getUrl(): string
    {
        $url = $this->source->parser_url;

        $tokens = explode(PHP_EOL, getenv('VK_TOKENS'));
        $tokens = array_values(array_filter(array_map(trim(...), $tokens)));
        if (!$tokens) {
            throw new \InvalidArgumentException('No VK tokens');
        }
        $accessToken = $tokens[rand(0, count($tokens) - 1)];

        if (
            mb_stripos($url, 'club') === 0
            || mb_stripos($url, 'public') === 0
        ) {
            $url = preg_replace("/(club){0,1}(public){0,1}(\d+)/i", "owner_id=-$3", $url);
        }

        if (!empty($customRequest)) {
            $url = "https://api.vk.com/method/{$customRequest}{$url}";
        } else {
            if (mb_stripos($url, 'owner_id=') !== false) {
                $url = "https://api.vk.com/method/wall.get?{$url}&count=10";
            } else {
                $url = "https://api.vk.com/method/wall.get?domain={$url}&count=10";
            }
        }
        $url .= "&v=5.157&access_token={$accessToken}";
        \Log::debug('url', [$url]);
        return $url;
    }

    protected function prepareSource(string $body, array $headers): string
    {
        return $body;
    }

    /**
     * @return Post[]
     */
    public function getPosts(): array
    {
        $json = json_decode($this->body, 1, JSON_THROW_ON_ERROR);
        $result = [];
        if (!array_key_exists('response', $json)) {
            \Log::error('invalid reponse', [
                'body' => $this->body,
            ]);
        }
        foreach ($json['response']['items'] as $item) {
            $post = $this->parsePost($item);
            if ($post) {
                $result[mb_strtolower($post->url)] = $post;
            }
        }

        return $result;
    }

    private function parsePost(array $item): ?Post
    {
        if (
            empty($item['owner_id'])
            || empty($item['id'])
        ) {
            return null;
        }

        while (!empty($item['copy_history'][0])) {
            $item['copy_history'][0]['text'] =
                ($item['text'] ? $item['text'] . ' <br>' : '') .
                ($item['copy_history'][0]['text'] ?
                    "Репост: " . '<br>' . $item['copy_history'][0]['text']
                    : ''
                );
            $item = $item['copy_history'][0];
        }


        $postData = [
            'url' => "https://vk.com/" .
                ($item['post_type'] != 'post' ? $item['post_type'] : 'wall') .
                $item['owner_id'] .
                "_" .
                $item['id'],
            'title' => "",
            'description' => preg_replace('/http[s]*:\/\/[^\s"]*/u', '', $item['text']),
            'image' => "",

        ];


        $postData['description'] = self::htmlToText($postData['description'], 750);

        $link_count = 0;
        $video_count = 0;
        $header_count = 0;
        $header = "";
        $attachmentChoosen = null;

        if (!empty($item['attachments'])) {
            foreach ($item['attachments'] as $index => $attachment) {
                if (!empty($attachment[$attachment['type']]['title'])) {
                    $header = $attachment[$attachment['type']]['title'];
                    $header_count++;
                }

                if ($attachment['type'] == "video") {
                    $video_count++;
                    $attachmentChoosen = $index;
                }

                if ($attachment['type'] == "link") {
                    $link_count++;
                    $attachmentChoosen = $index;
                }
            }

            switch ($item['attachments'][0]['type']) {
                case "doc":
                    $postData['image'] = end($item['attachments'][0]['doc']['preview']['photo']['sizes'])['src'];
                    break;
                case "photo":
                    $postData['image'] = end($item['attachments'][0]['photo']['sizes'])['url'];
                    break;
                case "video":
                    $postData['image'] = end($item['attachments'][0]['video']['image'])['url'];
                    break;
                case "link":
                    $photos = $item['attachments'][0]['link']['photo']['sizes'] ?? [];
                    $postData['image'] = $photos ? end($photos)['url'] : [];
                    break;

            }

            if ($link_count == 1 && $video_count == 0) {
                $postData['url'] = self::cropLink($item['attachments'][$attachmentChoosen]['link']['url']);
            }
            if ($video_count == 1 && $link_count == 0) {
                $attachment = $item['attachments'][$attachmentChoosen]['video'];
                $postData['url'] = "https://vk.com/video" . $attachment['owner_id'] . "_" . $attachment['id'];

            }
            $att_ext = (isset($item['attachments'][0][$item['attachments'][0]['type']]['ext']))
                ? $item['attachments'][0][$item['attachments'][0]['type']]['ext']
                : false;
            if ($att_ext) {
                $postData['title'] .= "[" . $att_ext . "] ";
            }

        }

        if (!empty($header) && $header_count == 1) {
            $postData['title'] .= self::htmlToText($header);
        } else if (!empty($postData['description'])) {
            $postData['title'] = self::htmlToText($postData['description']);
        }

        $post = Post::firstOrNew(['url' => $postData['url']]);
        $post->title = $postData['title'] ;
        $post->description = $postData['description'];
        $post->image = $postData['image'] ?: null;

        return $post;
    }

    private function htmlToText(string $text): string
    {
        $text = htmlspecialchars_decode(htmlspecialchars_decode($text));

        $text = strip_tags($text, '<br>');

        $breaks = ["<br />", "<br>", "<br/>"];
        $text = trim(str_ireplace($breaks, PHP_EOL, $text));
        $text = preg_replace("/([\s][\s])[\s]+/u", "$1", $text);
        $text = preg_replace('/http[s]*:\/\/[^\s"]*/u', '', $text);

        return $text;
    }
}
