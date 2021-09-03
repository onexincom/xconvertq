<?php

namespace App\Formatter;

use s9e\TextFormatter\Configurator;

class CustomerConfigurator
{

    public static function Customer(Configurator $configurator)
    {
        static::customeBBCodes($configurator);
        static::customerAllowHtml($configurator);
    }

    public static function customeBBCodes(Configurator $configurator)
    {
        $repository_bbcodes = [
            'B', 'I', 'S', 'FONT', 'COLOR', 'EM', 'EMAIL', 'QUOTE', 'LIST', 'FLOAT',
            'TABLE', 'TBODY', 'THEAD', 'TH', 'TR', 'TD',
            'ALIGN', 'BACKGROUND', 'CENTER', 'DEL', 'DL', 'DT',
            'H1', 'H2', 'H3', 'H4', 'H5', 'H6',
            'JUSTIFY', 'RIGHT', 'STRONG', 'SUB', 'SUP', 'UL',
            'SPOILER', 'IMG', 'URL', 'U', 'IMG', 'HR'
        ];
        foreach ($repository_bbcodes as $key => $code) {

            $configurator->BBCodes->addFromRepository($code);
        }

        $configurator->BBCodes->addCustom(
            '[font={PARSE=/(?<family>\w+)/}]{TEXT}[/font]',
            '<span style="font-family:{@family}">{TEXT}</span>'
        );

        $configurator->BBCodes->addCustom(
            '[indent]{TEXT}[/indent]',
            '<span>{TEXT}</span>'
        );

        $configurator->BBCodes->addCustom(
            '[size={RANGE=1,60}]{TEXT}[/size]',
            '<span style="font-size:{RANGE}">{TEXT}</span>'
        );

        $configurator->BBCodes->addCustom(
            '[sizepx={PARSE=/(?<size>\w+)/}]{TEXT}[/sizepx]',
            '<span style="font-size:{@size}px">{TEXT}</span>'
        );

        $configurator->BBCodes->addCustom(
            '[code]{TEXT}[/code]',
            "<pre style='overflow-x:scroll;'><code>{TEXT}</code></pre>"
        );

        $configurator->BBCodes->addCustom(
            '[backcolor={COLOR}]{TEXT}[/backcolor]',
            '<div style="background-color: {COLOR}">{TEXT}<div>'
        );

        $configurator->BBCodes->addCustom(
            '[url={URL}]{TEXT}[/url]',
            '<a href="{URL}">{TEXT}</a>'
        );

        $configurator->BBCodes->addCustom(
            '[password]{TEXT}[/password]',
            ''
        );
        $configurator->BBCodes->addCustom(
            '[audio]{URL}[/audio]',
            '<audio src="{URL}"></audio>'
        );

        $configurator->BBCodes->addCustom(
            '[free]{TEXT}[/free]',
            '<p>{TEXT}</p>'
        );

        $configurator->BBCodes->addCustom(
            '[hide]{TEXT}[/hide]',
            '<p>{TEXT}</p>'
        );

        $configurator->BBCodes->addCustom(
            '[index]{TEXT}[/index]',
            '<p>{TEXT}</p>'
        );

        $configurator->BBCodes->addCustom(
            '[qq]{TEXT}[/qq]',
            '<p>{TEXT}</p>'
        );

        $configurator->BBCodes->addCustom(
            '[media={PARSE=/(?<type>\w+),(?<width>\w+),(?<height>\w+)/}]{URL}[/media]',
            '<video src="{URL}" width="{@width}" height="{@height}"></video>'
        );

        $configurator->BBCodes->addCustom(
            '[p={PARSE=/(?<linheight>\w+), (?<indent>\w+), (?<align>\w+)/}]{TEXT}[/p]',
            '<p style="line-height:{@linheight}px;text-indent:{@indent}em;text-align:{@align}">{TEXT}</p>'
        );

        $configurator->BBCodes->addCustom(
            '[table={PARSE=/(?<width>\w+)/}]{TEXT}[/table]',
            '<table width="100%;">{TEXT}</table>'
        );

        $configurator->BBCodes->addCustom(
            '[flash={PARSE=/(?<width>\w+),(?<height>\w+)/}]{URL}[/flash]',
            '<embed src="{URL}" width="{@width}" height="{@height}"></embed>'
        );

    }

    public static function customerAllowHtml(Configurator $configurator)
    {
        $htmls = [
            'font' => [],
            'color' => [],
            'i' => [],
            'b' => [],
            'code' => [],
            'kbd' => [],
            'em' => [],
            'pre' => [],
            'small' => [],
            'strong' => [],
            'abbr' => [],
            'p' => [],
            'br' => [],
            'hr' => [],
            'ul' => [], 'li' => [], 'ol' => [],
            'dl' => [], 'dt' => [], 'dd' => [],
            'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
            'address' => [],
            'bdo' => [],
            'blockquote' => [],
            'cite' => [],
            'del' => [],
            'ins' => [],
            'sub' => [],
            'sup' => [],
            'a' => ['href'],
            'img' => ['width', 'height', 'loading', 'src', 'alt'],
            'div' => [],
            'table' => ['width', 'height', 'border'], 'thead' => [], 'tbody' => [], 'tr' => ['colspan', 'rowspan'], 'td' => [],
            'audio' => ['src', 'controls', 'width', 'height', 'loop'],
            'video' => ['src', 'controls', 'width', 'height'],
        ];

        foreach ($htmls as $element => $attrs) {
            $configurator->HTMLElements->allowElement($element);
            foreach ($attrs as $attr) {
                $configurator->HTMLElements->allowAttribute($element, $attr);
            }
        }
    }
}
