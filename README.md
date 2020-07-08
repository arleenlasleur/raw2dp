# raw2dp
IRRemote raw dumps converter

Launch: via PHP on CLI.
PHP required: 4+
System requirements: write permissions to current folder.

# Usage
Dump an IR code with IRrecvDump example and remove "Raw (68)" at the beginning
of the string, where 68 is length of RAW array. Strings then should be put in
text input file.

If proc_mark(0); appears at the end of code output function, that's because of
trailing space, remove it.
