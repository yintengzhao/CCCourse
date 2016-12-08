#!/bin/bash
rm -r output
NANOC_ENV=production NANOC_REMOTE=true nanoc compile
#git checkout gh-pages
cp -r output/. ../