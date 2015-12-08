#!/bin/bash
VERSION=$1

if [ "$VERSION" == "" ]; then
    echo "Please provide a version."
    exit 1
fi

CONTENTS=""

function get_content {
    cd $1
    DIR=${PWD##*/}
    echo -n "<dir name=\"${DIR}\">"
    for FILE in `ls`; do
        if [ -d $FILE ]; then
            get_content $FILE
        else
            HASH=`md5sum $FILE | awk '{ print $1 }'`
            echo -n "<file name=\"$FILE\" hash=\"$HASH\"/>"
        fi
    done
    echo -n "</dir>"
    cd ..
}

for dir in app shell; do
    CONTENTS="${CONTENTS}`get_content $dir`"
done

cat << EOF > package.xml
<?xml version="1.0"?>
<package>
    <name>CoScale</name>
    <version>$VERSION</version>
    <stability>beta</stability>
    <license>Commercial</license>
    <channel>community</channel>
    <extends/>
    <summary>This module enables you to send important business and IT metrics from Magento to CoScale.</summary>
    <description>The CoScale module exposes Magento events and metrics to the CoScale Agent. The metrics contain business metrics, such as the number of products, orders, abondoned carts, etc and technical metrics such as Magento caching metrics. The events contain magento admin actions such as page cache flushes, reindexing, etc.</description>
    <notes>The CoScale module is currently in Beta</notes>
    <authors><author><name>CoScale Developer</name><user>cs-dev</user><email>info@coscale.com</email></author></authors>
    <date>2015-08-13</date>
    <time>09:29:57</time>
    <contents><target name="mage"><dir name=".">$CONTENTS</dir></target></contents>
    <compatible/>
    <dependencies><required><php><min>5.3.0</min><max>6.0.0</max></php></required></dependencies>
</package>
EOF

sed -i "s/<version>.*<\/version>/<version>$VERSION<\/version>/" app/code/community/CoScale/Monitor/etc/config.xml

tar czf CoScale-${VERSION}.tgz package.xml app shell
rm package.xml
