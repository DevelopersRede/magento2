#!/usr/bin/env bash

tag=`date +"%m.%d"`
image=magento2

error()
{
	echo
	echo -e "\e[91mAlguma coisa aconteceu. Verifique.\e[39m">&2
	echo

	exit 1
}

package()
{
	if [ ! -z `git tag -l $1` ]; then
		echo
		echo -e "\e[91mA tag $1 já existe. Você precisa deletá-la antes de construir o pacote nessa versão.\e[39m">&2
		echo

		exit 1
	fi

	if [ ! command -v zip > /dev/null 2>&1 ]; then
		echo
		echo -e "\e[91mNão temos zip nesse sistema. Você precisará instalá-lo.\e[39m">&2
		echo

		exit 1
	fi

	cd src
	zip -r ../magento.zip *

	if [ $? -ne 0 ]; then
		error
	fi

	git tag -a $1 -m "Versão $1"

	if [ $? -ne 0 ]; then
		error
	fi

	git push origin $1
}

image()
{
	echo
	echo "Construindo a imagem $image:$tag."
	echo
	echo -e "\e[32mIsso pode demorar algum tempo...\e[39m"
	echo

	docker build -t $image:latest .

	if [ $? -ne 0 ]; then
		error
	fi

	docker tag $image:latest $image:$tag

	if [ $? -ne 0 ]; then
		error
	fi

	echo
	echo -e "\e[32mImagem construída com sucesso.\e[39m"
	echo
}

using()
{
	echo
	echo "Utilização:"
	echo
	echo "$0 image  		Para construir a imagem Docker"
	echo "$0 package version	Para construir o pacote. Especifique a versão do pacote"
	echo

	exit 1
}

if [[ -z $1 ]] ; then
	using
else
	case $1 in
		help)
			using
			;;
		image)
			image
			;;
		package)
			if [[ -z $2 ]]; then
				using
			fi

			package $2
			;;
		*)
			;;
	esac
fi
