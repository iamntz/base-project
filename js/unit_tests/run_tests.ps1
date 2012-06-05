Import-Module pswatch

watch  | %{
  cls
  phantomjs.exe testrunner.js test.html
}