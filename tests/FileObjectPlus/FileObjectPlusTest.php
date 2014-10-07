<?php

/**
 * Class FileObjectPlusTest
 */
class FileObjectPlusTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \DCarbone\FileObjectPlus::__construct
     * @uses \DCarbone\FileObjectPlus
     * @uses \SplFileObject
     * @return \DCarbone\FileObjectPlus
     */
    public function testCanConstructFileObjectPlusWithValidFilenameParameter()
    {
        $fileObject = new \DCarbone\FileObjectPlus(__DIR__.'/../misc/example.txt');

        return $fileObject;
    }

    /**
     * @covers \DCarbone\FileObjectPlus::__construct
     * @uses \DCarbone\FileObjectPlus
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionThrownWhenNonStringFilenameValuePassed()
    {
        $fileObject = new \DCarbone\FileObjectPlus(array('nossir'));
    }

    /**
     * @covers \DCarbone\FileObjectPlus::__construct
     * @covers \SplFileObject::__construct
     * @uses \DCarbone\FileObjectPlus
     * @uses \SplFileObject
     * @expectedException \RuntimeException
     */
    public function testExceptionThrownWhenNonExistentFilenamePassed()
    {
        $fileObject = new \DCarbone\FileObjectPlus('nope.txt');
    }

    /**
     * @covers \DCarbone\FileObjectPlus::getLineCount
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testCanGetLineCount(\DCarbone\FileObjectPlus $fileObject)
    {
        $lineCount = $fileObject->getLineCount();

        $this->assertEquals(50, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::getLineCountLike
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testGetLineCountLikeWithStringThatExistsInFile(\DCarbone\FileObjectPlus $fileObject)
    {
        $lineCount = $fileObject->getLineCountLike('lj1036.inktomisearch.com');

        $this->assertEquals(1, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::getLineCountLike
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testGetLineCountLikeReturnsZeroWithStringThatDoesNotExistInFile(\DCarbone\FileObjectPlus $fileObject)
    {
        $lineCount = $fileObject->getLineCountLike('this string doesn\'t exist!');

        $this->assertEquals(0, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::getLineCountLike
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testGetLineCountLikeReturnsAllLinesWithEmptyStringValue(\DCarbone\FileObjectPlus $fileObject)
    {
        $lineCount = $fileObject->getLineCountLike(false);

        $this->assertEquals(50, $lineCount);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::getLineCountLike
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @expectedException \InvalidArgumentException
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function textExceptionThrownByGetLineCountLikeWithNonScalarParameter(\DCarbone\FileObjectPlus $fileObject)
    {
        $lineCount = $fileObject->getLineCountLike(array('nope'));
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testPaginateLinesWithDefaultParameters(\DCarbone\FileObjectPlus $fileObject)
    {
        $lines = $fileObject->paginateLines();

        $this->assertCount(25, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testPaginateLinesWithDefaultOffsetAndLimitWithSearchTerm(\DCarbone\FileObjectPlus $fileObject)
    {
        $lines = $fileObject->paginateLines(0, 25, 'hsdivision');

        $this->assertCount(1, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testPaginateLinesWithDefaultOffsetReducedLimitAndDefaultSearchTerm(\DCarbone\FileObjectPlus $fileObject)
    {
        $lines = $fileObject->paginateLines(0, 5);

        $this->assertCount(5, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @covers \DCarbone\FileObjectPlus::paginateLinesNoSearch
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testPaginateLinesWithIncreasedOffsetAndDefaultLimitAndDefaultSearchTerm(\DCarbone\FileObjectPlus $fileObject)
    {
        $lines = $fileObject->paginateLines(40);

        $this->assertCount(9, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @covers \DCarbone\FileObjectPlus::paginateLinesSearch
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testPaginateLinesWithIncreasedOffsetAndDefaultLimitWithSearchTerm(\DCarbone\FileObjectPlus $fileObject)
    {
        $lines = $fileObject->paginateLines(12, 25, '/twiki/bin/view/');

        $this->assertCount(4, $lines);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @expectedException \InvalidArgumentException
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testExceptionThrownByPaginateLinesWithInvalidIntegerFirstArgument(\DCarbone\FileObjectPlus $fileObject)
    {
        $list = $fileObject->paginateLines(-7);
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @expectedException \InvalidArgumentException
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testExceptionThrownByPaginateLinesWithNonIntegerFirstArgument(\DCarbone\FileObjectPlus $fileObject)
    {
        $list = $fileObject->paginateLines('forty seven');
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @expectedException \InvalidArgumentException
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testExceptionThrownByPaginateLinesWithNonIntegerSecondArgument(\DCarbone\FileObjectPlus $fileObject)
    {
        $list = $fileObject->paginateLines(0, 'seventy 2');
    }

    /**
     * @covers \DCarbone\FileObjectPlus::paginateLines
     * @uses \DCarbone\FileObjectPlus
     * @depends testCanConstructFileObjectPlusWithValidFilenameParameter
     * @expectedException \InvalidArgumentException
     * @param \DCarbone\FileObjectPlus $fileObject
     */
    public function testExceptionThrownByPaginateLinesWithInvalidIntegerSecondArgument(\DCarbone\FileObjectPlus $fileObject)
    {
        $list = $fileObject->paginateLines(0, -42);
    }
}