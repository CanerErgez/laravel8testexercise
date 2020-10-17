<?php


namespace App\Services;


use Exception;

class ColorConvertService
{
    const HASH = '#';
    const AVAILABLE_LENGTH = [3, 6];
    const HEX_PATTERN = '/^[0-9a-fA-F]+$/s';

    protected $hex;
    protected $alpha;

    public function __construct(string $hex, $alpha)
    {
        $this->hex = $hex;
        $this->alpha = $alpha;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function hexToRgba(): string
    {
        $this->cleanHash();
        $this->isValidHex();
        $this->convertAlphaToFloat();

        return $this->convertHexToRgba();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function convertAlphaToFloat()
    {
        $this->alpha = (float)$this->alpha;

        if ($this->alphaIsValid()) {
            return;
        }

        throw new Exception('Alpha is between 0 and 1');
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function alphaIsValid(): bool
    {
        return $this->alpha >= 0 && $this->alpha <= 1;
    }

    /**
     * @return void
     */
    public function cleanHash(): void
    {
        if (substr($this->hex, 0, 1) == self::HASH) {
            $this->hex = substr($this->hex, 1);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function isValidHex()
    {
        $this->isValidLength();
        $this->isValidDeclaration();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function isValidLength()
    {
        if (in_array(strlen($this->hex), self::AVAILABLE_LENGTH)) {
            return;
        }

        throw new Exception('Hex length is not valid.');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function isValidDeclaration()
    {
        if (preg_match(self::HEX_PATTERN, $this->hex)) {
            return;
        }

        throw new Exception('Hex value is not valid.');
    }

    /**
     * @return string
     */
    public function convertHexToRgba(): string
    {
        return 'rgba('.
            $this->hexToInteger(1).','.
            $this->hexToInteger(2).','.
            $this->hexToInteger(3).','.
            $this->alpha.')';
    }

    /**
     * @param int $iterator
     * @return int
     */
    public function hexToInteger(int $iterator): int
    {
        if (strlen($this->hex) === 3) {
            $character = substr($this->hex, $iterator-1, 1);
            $character .= $character;

            return hexdec($character);
        }

        return hexdec(substr($this->hex, $iterator*2-2, 2));
    }
}
