<?php
namespace Bookdown\Bookdown\Processor;

use Bookdown\Bookdown\Content\Page;
use League\CommonMark\CommonMarkConverter;

class HtmlProcessor
{
    protected $commonMarkConverter;

    public function __construct(CommonMarkConverter $commonMarkConverter)
    {
        $this->commonMarkConverter = $commonMarkConverter;
    }

    public function __invoke(Page $page)
    {
        $text = $this->readOriginFile($page);
        $html = $this->commonMarkConverter->convertToHtml($text);
        $this->saveTargetFile($page, $html);
    }

    protected function readOriginFile(Page $page)
    {
        $file = $page->getOrigin();
        if (! $file) {
            return;
        }

        $level = error_reporting(0);
        $text = file_get_contents($file);
        error_reporting($level);

        if ($text !== false) {
            return $text;
        }

        $error = error_get_last();
        throw new Exception($error['message']);
    }

    protected function saveTargetFile(Page $page, $html)
    {
        $file = $page->getTargetFile();
        $dir = dirname($file);
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($file, $html);
    }
}