<?php
/**
 * Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPca
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\PHPca;

/**
 * The Token class wraps one PHP tokenizer token.
 * Each token knows its line and column in the source file.
 * Where no PHP tokens exist (brackets, braces, etc.),
 * we have defined our own in Constants.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @todo a token should know the function/method it is in
 * @todo a token should know the class it is in
 * @todo a token should know the namespace it is in (probably part of fn and class name thing)
 */
class Token
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var int
     */
    protected $line;

    /**
     * @var int
     */
    protected $column;

    /**
     * Constructs the object
     *
     * @param int $id
     * @param string $text
     * @param int $line
     * @param int $column
     * @return void
     */
    public function __construct($id, $text, $line = 0, $column = 0)
    {
        $this->id       = $id;
        $this->text     = $text;
        $this->line     = $line;
        $this->column   = $column;
    }

    /**
     * Sets the file this token is part of.
     *
     * @param File $file The file the token is part of
     * @return void
     */
    public function setFile(File $file)
    {
        $this->file = $file;
    }

    /**
     * Returns the file this token is part of.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns the numeric ID of the constant representing this token.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the name of the constant representing this token as a string.
     *
     * @return string
     */
    public function getName()
    {
       return Constants::getTokenName($this->id);
    }

    /**
     * Returns the text (contents) of this token.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Returns the source code line the token is located on.
     *
     * @return int
     */
    public function getLine()
    {
        // For whitespace with leading line breaks, we display
        // the "next" line number instead of the actual line number where the whitespace token starts
        if ($this->id == T_WHITESPACE) {
            preg_match("/^\\n*/m", $this->text, $matches);
            return $this->line + strlen($matches[0]);
        }

        return $this->line;
    }

    /**
     * Returns the source code column the token is located on.
     *
     * @return int
     */
    public function getColumn()
    {
        // If whitespace starts with newlines, we claim to be on colum 1 of the "next" line
        if ($this->id == T_WHITESPACE) {
            preg_match("/^\\n*/m", $this->text, $matches);
            if (strlen($matches[0]) > 0) {
                return 1;
            }
        }

        return $this->column;
    }

    /**
     * Returns the length in characters of this token.
     *
     * @return int
     */
    public function getLength()
    {
       return strlen($this->text);
    }

    /**
     * Check whether the token contains any new line characters.
     *
     * @return int
     */
    public function hasNewLine()
    {
        return strstr($this->text, "\n") !== false;
    }

    /**
      * Returns new line count of the token's text.
      * Only counts \n, no \r characters.
      *
      * @return int
      */
    public function getNewLineCount()
    {
        return substr_count($this->text, "\n");
    }

    /**
     * Check whether the token's text contains whitespace
     * (CR, LF, tab, or blank).
     *
     * @return bool
     */
    public function hasWhitespace()
    {
        return strstr($this->text, "\r") !== false ||
               strstr($this->text, "\n") !== false ||
               strstr($this->text, "\t") !== false ||
               strstr($this->text, " ")  !== false;
    }

    /**
     * Returns the trailing whitespace, that is the whitespace after
     * the last new line (if present).
     *
     * @return string
     */
    public function getTrailingWhitespace()
    {
        if (!$this->hasNewLine()) {
            preg_match('/\s*$/', $this->text, $matches);
            return $matches[0];
        }

        $pos = strrpos($this->text, "\n");

        if ($pos >= strlen($this->text) - 1) {
            return '';
        }

        return substr($this->text, $pos + 1);
    }

    /**
     * Returns count of trailing whitespace characters.
     *
     * @return int
     */
    public function getTrailingWhitespaceCount()
    {
        return strlen($this->getTrailingWhitespace());
    }
}
?>