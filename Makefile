######################################################
# Start edit here
VENDOR:=Dhl
MODULE:=OnlineRetoure
ARCHIVE_COLLECTION:=src/app
# End edit here
######################################################

######################################################
# Build script variables
MODULE_KEY:=$(VENDOR)_$(MODULE)
DATE:=$(shell date +%s)
VERSION:=$(shell grep "<version>" src/app/code/community/$(VENDOR)/$(MODULE)/etc/config.xml | sed -e :a -e 's/<[^>]*>//g;/</N;//ba;s/ //g')

#Paths, zip-file and tar-file
MODULE_PATH:=$(shell pwd)
TMPPATH:=/tmp/$(MODULE).$(DATE)
ZIPNAME:=$(MODULE)-$(VERSION).zip
TARNAME:=$(MODULE)-$(VERSION).tar
ZIPFILE:=/tmp/$(ZIPNAME)
TARFILE:=/tmp/$(TARNAME)

# Doc folders
DOCPATH:=doc
DOC_PUBLIC_PATH:=$(DOCPATH)/$(MODULE_KEY)
DOC_INTERN_PATH:=$(DOCPATH)/Intern
DOC_SOURCE_PATH:=$(DOCPATH)/src
######################################################

all: clean version doc zip tar

doc: $(DOC_PUBLIC_PATH)/OnlineRetoure.pdf $(DOC_PUBLIC_PATH)/ChangeLog.pdf

$(DOC_PUBLIC_PATH)/OnlineRetoure.pdf: $(DOC_SOURCE_PATH)/OnlineRetoure.rst $(DOC_SOURCE_PATH)/dhl.style $(DOC_SOURCE_PATH)/images/*
		rst2pdf -b 1 -o $(DOC_PUBLIC_PATH)/OnlineRetoure.pdf -s $(DOC_SOURCE_PATH)/dhl.style $(DOC_SOURCE_PATH)/OnlineRetoure.rst --real-footnotes

$(DOC_PUBLIC_PATH)/ChangeLog.pdf: $(DOC_SOURCE_PATH)/ChangeLog.rst $(DOC_SOURCE_PATH)/dhl.style
		rst2pdf -b 1 -o $(DOC_PUBLIC_PATH)/ChangeLog.pdf -s $(DOC_SOURCE_PATH)/dhl.style $(DOC_SOURCE_PATH)/ChangeLog.rst --real-footnotes





clean:
		rm -f $(DOC_PUBLIC_PATH)/*.pdf
		rm -f $(DOC_INTERN_PATH)/*.pdf

version:
		@echo === Making $(MODULE_KEY) version $(VERSION)

zip:
		@echo === Creating zip file $(ZIPFILE) from $(TMPPATH)
		rm -f $(ZIPFILE)
		rm -f $(ZIPNAME)
		rm -rf $(TMPPATH)
		mkdir -p $(TMPPATH)/doc
		cp -r $(ARCHIVE_COLLECTION) $(TMPPATH)
		cp -r doc/$(MODULE_KEY) $(TMPPATH)/doc/
		rm -f $(TMPPATH)/doc/$(MODULE_KEY)/.gitkeep

		cd $(TMPPATH) && zip -rq $(ZIPFILE) *
		rm -rf $(TMPPATH)
		cd $(MODULE_PATH)
		cp -f $(ZIPFILE) $(ZIPNAME)
		rm -f $(ZIPFILE)

tar:
		@echo === Creating tar file $(TARFILE) from $(TMPPATH)
		rm -f $(TARFILE)
		rm -f $(TARNAME)
		rm -rf $(TMPPATH)
		mkdir -p $(TMPPATH)/doc
		cp -r $(ARCHIVE_COLLECTION) $(TMPPATH)
		cp -r doc/$(MODULE_KEY) $(TMPPATH)/doc/
		rm -f $(TMPPATH)/doc/$(MODULE_KEY)/.gitkeep

		cd $(TMPPATH) && tar -cf $(TARFILE) *
		rm -rf $(TMPPATH)
		cd $(MODULE_PATH)
		cp -f $(TARFILE) $(TARNAME)
		rm -f $(TARFILE)

.PHONY: doc
