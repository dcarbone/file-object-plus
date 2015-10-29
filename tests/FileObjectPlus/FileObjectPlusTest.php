<?php

/*
    Modified SplFileObject class that adds Countable interface and Pagination methods
    Copyright (C) 2013-2015  Daniel Paul Carbone (daniel.p.carbone@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 */

/**
 * Class FileObjectPlusTest
 */
class FileObjectPlusTest extends PHPUnit_Framework_TestCase
{
    /** @var \DCarbone\FileObjectPlus */
    protected $fileObject;

    /**
     * Initialize local instance of FileObjectPlus
     *
     * Since we are not overloading the constructor in any way, this should not pose a problem...
     */
    protected function setUp()
    {
        $this->fileObject = new \DCarbone\FileObjectPlus(__DIR__.'/../misc/example.txt');
    }

    /**
     * @covers \DCarbone\FileObjectPlus::count
     */
    public function testCanGetLineCount()
    {
        $this->assertEquals(50, count($this->fileObject));
    }

    /**
     * @covers \DCarbone\FileObjectPlus::countLinesContaining
     */
    public function testGetLineCountLikeWithStringThatExistsInFile()
    {
        $lineCount = $this->fileObject->countLinesContaining('lj1036.inktomisearch.com');

        $this->assertEquals(1, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::countLinesContaining
     */
    public function testGetLineCountLikeReturnsZeroWithStringThatDoesNotExistInFile()
    {
        $lineCount = $this->fileObject->countLinesContaining('this string doesn\'t exist!');

        $this->assertEquals(0, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::countLinesContaining
     */
    public function testGetLineCountLikeReturnsAllLinesWithEmptyStringValue()
    {
        $lineCount = $this->fileObject->countLinesContaining('');

        $this->assertEquals(50, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::countLinesContaining
     */
    public function textExceptionThrownByGetLineCountLikeWithNonScalarParameter()
    {
        $this->fileObject->countLinesContaining(array('nope'));
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     */
    public function testPaginateLinesWithDefaultParameters()
    {
        $lines = $this->fileObject->paginateLines();
        $this->assertCount(25, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     */
    public function testPaginateLinesWithDefaultOffsetAndLimitWithSearchTerm()
    {
        $lines = $this->fileObject->paginateLines(0, 25, 'hsdivision');
        $this->assertCount(1, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     */
    public function testPaginateLinesWithDefaultOffsetReducedLimitAndDefaultSearchTerm()
    {
        $lines = $this->fileObject->paginateLines(0, 5);
        $this->assertCount(5, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @covers \DCarbone\FileObjectPlus::paginateLinesNoSearch
     */
    public function testPaginateLinesWithIncreasedOffsetAndDefaultLimitAndNoSearchTermIncludingEmptyLines()
    {
        $lines = $this->fileObject->paginateLines(40);
        $this->assertCount(10, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @covers \DCarbone\FileObjectPlus::paginateLinesNoSearch
     */
    public function testPaginateLinesWithIncreasedOffsetAndDefaultLimitAndNoSearchTermExcludingEmptyLines()
    {
        $lines = $this->fileObject->paginateLines(40, 25, null, false);
        $this->assertCount(9, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @covers \DCarbone\FileObjectPlus::paginateLinesSearch
     */
    public function testPaginateLinesWithIncreasedOffsetAndDefaultLimitWithSearchTerm()
    {
        $lines = $this->fileObject->paginateLines(12, 25, '/twiki/bin/view/');

        $this->assertCount(4, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownByPaginateLinesWithInvalidIntegerFirstArgument()
    {
        $this->fileObject->paginateLines(-7);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownByPaginateLinesWithNonIntegerFirstArgument()
    {
        $this->fileObject->paginateLines('forty seven');
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownByPaginateLinesWithNonIntegerSecondArgument()
    {
        $this->fileObject->paginateLines(0, 'seventy 2');
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownByPaginateLinesWithInvalidIntegerSecondArgument()
    {
        $this->fileObject->paginateLines(0, -42);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::countLinesContaining
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownWhenPassingNonStringCastableValueToLineCountSearch()
    {
        $this->fileObject->countLinesContaining(new \SplFixedArray());
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @covers \DCarbone\FileObjectPlus::paginateLinesSearch
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownWhenPassingNonStringCastableValueToPaginateSearch()
    {
        $this->fileObject->paginateLines(0, 25, new \SplFixedArray());
    }
}