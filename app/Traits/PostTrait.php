<?php
/**
 * Discuz!x 转 Q 涉及到bbcode转换可修改该文件
 * 
 * 查阅文档
 * https://github.com/s9e/TextFormatter
 * https://s9etextformatter.readthedocs.io/Plugins/BBCodes/Synopsis/
 */
namespace App\Traits;

// use s9e\TextFormatter\Bundles\Forum as TextFormatter;
use s9e\TextFormatter\Configurator;
use App\Formatter\CustomerConfigurator;
use App\Models\DiscuzQ\Attachment;
use App\Models\DiscuzX\CommonSmiley;
use App\Models\DiscuzX\ForumImageType;

trait PostTrait
{

    static $emoji = [];

    static $emoji_type = [];

    public $configurator_result = null;

    public $reply_info = [];

    /**
     * 设置解析
     */
    public function setConfig()
    {
        if (is_null($this->configurator_result)) {
            $configurator = new Configurator();
            CustomerConfigurator::Customer($configurator);
            foreach (CommonSmiley::query()->cursor() as $smile) {
                $code = preg_replace(['/\{/', '/\}/'], ['[', ']'], $smile->code);
                $emojiImg = '<img style="display:inline-block;vertical-align:top;" src="" alt="" class="qq-emotion"/>';
                $configurator->Emoticons->add($code, $emojiImg);
            }

            $this->configurator_result = $configurator->finalize();
        }
    }

    /**
     * @param $message
     * @param null $type
     * @return string
     * 转换内容
     */
    public function convertMessage($message, $type = null)
    {
        if (is_null($this->configurator_result)) {
            $this->setConfig();
        }

        $message = $this->replaceFotnFamily($message);
        $message = $this->replaceAttache($message);
        $message = $this->replaceEmoji($message);

        $message = $this->replaceNewline($message);
        
        $message = $this->fixUrl($message);
        $message = $this->fixImg($message);
        //$message = $this->fixSize($message);
        
        // discuz!x bbcode转html方法
        $message = $this->fixCode($message);
        
        // Q 3.0
        // 解析标签：table / 
        //$message = TextFormatter::parse($message);
        //$message = TextFormatter::render($message);
        
        return '<p>'.nl2br($message).'</p>';
        
        // Q 2.0 
        // bbcode --> xml
        $parser = $this->configurator_result['parser'];
        $xml = $parser->parse($message);
        // xml --> bbcode
        // tips
        // extension=xsl
        $renderer = $this->configurator_result['renderer'];
        $html = $renderer->render($xml);
        return $html;
    }
    

    /**
     * discuz!x bbcode转html方法
     * @param $message
     * @return string|string[]|null
     * 中文字体替换
     */
    public function fixCode($message)
    {
        $message = str_replace(array(
            '[/color]', '[/backcolor]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]', '[s]', '[/s]', '[hr]', '[/p]',
            '[i=s]', '[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
            '[list=A]', "\r\n[*]", '[*]', '[/list]', '[indent]', '[/indent]', '[/float]'
            ), array(
            '</font>', '</font>', '</font>', '</font>', '</div>', '<strong>', '</strong>', '<strike>', '</strike>', '<hr class="l" />', '</p>', '<i class="pstatus">', '<i>',
            '</i>', '<u>', '</u>', '<ul>', '<ul type="1" class="litype_1">', '<ul type="a" class="litype_2">',
            '<ul type="A" class="litype_3">', '<li>', '<li>', '</ul>', '<blockquote>', '</blockquote>', '</span>'
            ), preg_replace(array(
            "/\[color=([#\w]+?)\]/i",
            "/\[color=((rgb|rgba)\([\d\s,]+?\))\]/i",
            "/\[backcolor=([#\w]+?)\]/i",
            "/\[backcolor=((rgb|rgba)\([\d\s,]+?\))\]/i",
            "/\[size=(\d{1,2}?)\]/i",
            "/\[size=(\d{1,2}(\.\d{1,2}+)?(px|pt)+?)\]/i",
            "/\[font=([^\[\<]+?)\]/i",
            "/\[align=(left|center|right)\]/i",
            "/\[p=(\d{1,2}|null), (\d{1,2}|null), (left|center|right)\]/i",
            "/\[float=left\]/i",
            "/\[float=right\]/i"

            ), array(
            "<font color=\"\\1\">",
            "<font style=\"color:\\1\">",
            "<font style=\"background-color:\\1\">",
            "<font style=\"background-color:\\1\">",
            "<font size=\"\\1\">",
            "<font style=\"font-size:\\1\">",
            "<font face=\"\\1\">",
            "<div align=\"\\1\">",
            "<p style=\"line-height:\\1px;text-indent:\\2em;text-align:\\3\">",
            "<span style=\"float:left;margin-right:5px\">",
            "<span style=\"float:right;margin-left:5px\">"
            ), $message));
        
        // [code]
        $message = preg_replace_callback("/\s?\[code\](.+?)\[\/code\]\s?/is", function($matches){
                return '<pre><code>'.htmlspecialchars($matches[1]).'</code></pre>';
            }, $message);
        // note 回贴中只插入code代码时，Q内容显示为空
        
        return $message;
    }

    /**
     * @param $message
     * @return string|string[]|null
     * 中文字体替换
     */
    public function replaceFotnFamily($message)
    {
        $fonts = [
            '宋体' => 'SimSun',
            '新宋体' => 'NSimSun',
            '黑体' => 'SimHei',
            '微软雅黑体' => 'Microsoft YaHei',
            '微软雅黑' => 'Microsoft YaHei',
            '仿宋_GB2312' => 'FangSong_GB2312',
            '楷体_GB2312' => 'KaiTi_GB2312'
        ];

        return preg_replace_callback('%\[font=([\W\D\w\s]*?)\]([\W\D\w\s]*?)\[/font\]%iu',
            function ($matches) use ($fonts) {
                $matches[1] = str_replace(['&quot;', ' '], ['', ''], $matches[1]);
                if (empty($matches[1])) {
                    return $matches[2];
                }
                if (isset($fonts[$matches[1]])) {
                    return '[font=' . $fonts[$matches[1]] . ']' . $matches[2] . '[/font]';
                } else {
                    return '[font=' . $matches[1] . ']' . $matches[2] . '[/font]';
                }
            },
            $message
        );
    }

    /**
     * @param $message
     * @return array
     * 匹配回复数据
     */
    public function findReply($message)
    {
        $message = preg_replace_callback("/^\[quote\]([\s\S]*?)\[\/quote\]/", [$this, 'setReply'], $message, 1);
        $result = [
            'message' => trim($message),
            'reply_info' => $this->reply_info
        ];
        $this->reply_info = [];
        return $result;
    }


    private function setReply($matches)
    {
        if (isset($matches[1])) {
            preg_match("/\&pid=(\d+)\&/", $matches[1], $pid);
            //preg_match("/\&ptid=(\d+)\]/", $matches[1], $tid);
            preg_match("/999999](.*)发表于/", $matches[1], $username);

            if (isset($pid[1]) && isset($username[1])) {
                $this->reply_info = [
                    'pid' => isset($pid[1]) ? $pid[1] : '',
                    //'tpid' => isset($tid[1]) ? $tid[1] : '',
                    'username' => isset($username[1]) ? $username[1] : '',
                ];
            }
        }
        return '';
    }

    public function replaceNewline($text)
    {
        return preg_replace_callback('%\r\n|\r|\n%iu',
            function ($matches) {
                return "\n";
                return '<p></p>';
            },
            $text
        );
    }

    /**
     * @param $text
     * @return string|string[]|null
     * 规范化 url 标签
     */
    public function fixUrl($text)
    {
        return preg_replace_callback('%\[url\]([\s\S]*?)\[\/url\]%iu',
            function ($matches) {
                return '<a href=' . $matches[1] . '>' . $matches[1] . '</a>';
                //return '[url=' . $matches[1] . ']' . $matches[1] . '[/url]';
            },
            $text
        );
    }

    /**
     * @param $text
     * @return string|string[]|null
     * 规范化 img 标签
     */
    public function fixImg($text)
    {
        return preg_replace_callback('%\[img=(\d+),(\d+)\]([\s\S]*?)\[\/img\]%iu',
            function ($matches) {
                if ($matches[1] == 0) {
                    $matches[1] = '100%';
                }
                if ($matches[2] == 0) {
                    $matches[2] = '100%';
                }
                return '<img width="' . $matches[1] . '" height="' . $matches[2] . '" src="' . $matches[3] . '>';
                //return '[img width="' . $matches[1] . '" height="' . $matches[2] . '" ]' . $matches[3] . '[/img]';
            },
            $text
        );
    }

    /**
     * @param $text
     * @return string|string[]|null
     * 规范化 img 标签
     */
    public function fixSize($text)
    {
        return preg_replace_callback('%\[size=(\d+)px\]([\s\S]*?)\[\/size\]%iu',
            function ($matches) {
                return '<font style="font-size: ' . $matches[1] . 'px;">' . $matches[2] . '</font>';
                //return '[sizepx=' . $matches[1] . ']' . $matches[2] . '[/sizepx]';
            },
            $text
        );
    }

    /**
     * @param $text
     * @return string|string[]|null
     * 图片 bbcode 替换
     */
    public function replaceAttache($text)
    {
        return preg_replace_callback('%\[attach\](\d+)\[\/attach\]%iu',
            function ($matches) {
                $attachment = Attachment::find($matches[1]);
                if ($attachment) {
                    if ($attachment->type == Attachment::TYPE_OF_IMAGE) {
                        return '[img alt="' . $matches[1] . '" src="http://discuz" title="' . $matches[1] . '" width="" height=""][/img]';
                    }
                } else {
                    return;
                }
            },
            $text
        );
    }

    /**
     * @param $text
     * @return string|string[]|null
     * 表情符号
     */
    public function replaceEmoji($text)
    {
        if (empty(static::$emoji_type)) {
            foreach (ForumImageType::query()->cursor() as $type) {
                static::$emoji_type[$type->typeid] = $type->directory;
            }
        }

        if (empty(static::$emoji) && static::$emoji_type) {
            foreach (CommonSmiley::query()->cursor() as $smile) {
                static::$emoji['search'][$smile->id] = '/' . preg_quote(htmlspecialchars($smile->code), '/') . '/';
                $code = preg_replace(['/\{/', '/\}/'], ['[', ']'], $smile->code);
                static::$emoji['replace'][$smile->id] = $code;
            }
        }
        return preg_replace(static::$emoji['search'], static::$emoji['replace'], $text, 100);
    }
}