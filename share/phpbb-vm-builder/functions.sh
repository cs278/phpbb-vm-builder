function include_file()
{
	SRC=$1;
	NAME=$(basename "$SRC");

	if [ -n "$2" ]; then
		DST="/root/${2//\//_}";
	else
		if [ "${2:0:1}" != "/" ]; then
			DST="/root/$2";
		else
			DST=$2;
		fi
	fi

	echo "$SRC $DST" >> $COPY;
	echo -n $DST;
}

function debconf_set()
{
	PACKAGE=$1;
	FILE="$CONFIG/packages/$PACKAGE/selections";

	if [ -e $FILE ]; then
		echo "cat \"$FILE\" | chroot \$1 debconf-set-selections" >> $EXEC;
	fi
}

function boot_install_pre()
{
	PACKAGE=$1;
	FILE="$CONFIG/packages/$PACKAGE/install.pre";

	if [ -e $FILE ]; then
		run_in_target $FILE;
	fi;
}

function boot_install()
{
	PACKAGE=$1;

	echo "# Install '$PACKAGE'" >> $EXEC;
	echo "" >> $EXEC;

	debconf_set $PACKAGE;

	boot_install_pre $PACKAGE;

	echo "chroot \$1 apt-get install -y --force-yes $PACKAGE;" >> $EXEC;
	echo "chroot \$1 dpkg-reconfigure --unseen-only --priority=critical $PACKAGE;" >> $EXEC;

	boot_install_post $PACKAGE;

	echo "" >> $EXEC;
	echo "# Done installing '$PACKAGE'" >> $EXEC;
	echo "" >> $EXEC;
}

function boot_install_post()
{
	PACKAGE=$1;
	FILE="$CONFIG/packages/$PACKAGE/install.post";

	if [ -e $FILE ]; then
		run_in_target $FILE;
	fi;
}

function run_in_target()
{
	FILE=$1;

	if [ -e $FILE ]; then
		echo "TMP=\$(chroot \$1 mktemp);" >> $EXEC;
		echo "cp $FILE \$TMP;" >> $EXEC;
		echo "chroot \$1 chmod o+x \$TMP;" >> $EXEC;
		echo "chroot \$1 \$TMP;" >> $EXEC;
		echo "chroot \$1 rm \$TMP;" >> $EXEC;
	fi;
}
