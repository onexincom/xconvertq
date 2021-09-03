<?php

namespace App\Models\DiscuzX;

class ForumAttachment extends DiscuzxBaseModel
{
    protected $table = "forum_attachment";

    /**
     * 重定义主键
     *
     * @var string
     */
    protected $primaryKey = 'aid';

    public static function convertAttachment()
    {
        return static::query()->where('tableid', '<=', 10);
    }

    public function realAttachment()
    {
        $tableid = $this->tableid;

        switch ($tableid) {
            case 0:
                $attachment_model = ForumAttachmentA::class;
                break;
            case 1:
                $attachment_model = ForumAttachmentB::class;
                break;
            case 2:
                $attachment_model = ForumAttachmentC::class;
                break;
            case 3:
                $attachment_model = ForumAttachmentD::class;
                break;
            case 4:
                $attachment_model = ForumAttachmentE::class;
                break;
            case 5:
                $attachment_model = ForumAttachmentF::class;
                break;
            case 6:
                $attachment_model = ForumAttachmentG::class;
                break;
            case 7:
                $attachment_model = ForumAttachmentH::class;
                break;
            case 8:
                $attachment_model = ForumAttachmentI::class;
                break;
            case 9:
                $attachment_model = ForumAttachmentJ::class;
                break;
            default:
                $attachment_model = '';
                break;
        }
        if (!empty($attachment_model)) {
            return $this->hasOne($attachment_model, 'aid');
        }
    }

}