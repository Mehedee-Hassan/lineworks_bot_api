#　個人
# command ./trylineworksbot.sh "test test" id@server 1
# グループ：
# ./lineworksbot.sh "test test" channelID 0


secret="-----BEGIN PRIVATE KEY-----\n\n-----END PRIVATE KEY-----"
BOTNO=""
SERVERID=""
CONSUMERKEY=""
message_str=$1
# message_str=$(echo '店舗コード'| iconv -f euc-jp -t utf-8)

#CHANNELID="117439512"
#117478754

ACCOUNT_ID=$2
# ACCOUNT_ID=""
# IS_CHANNEL=$3



slen=0
while [ "$slen" != 511 ]
do

apiid="jp1LXyuohwFqr"
iat=$(date +%s)
exp=$(date --date="+3500 seconds" '+%s')

MESSAGE_SEND_URL="https://apis.worksmobile.com/r/${CONSUMERKEY}/message/v1/bot/${BOTNO}/message/push"
url_accesstoken="https://auth.worksmobile.com/b/"${apiid}"/server/token"





# Static header fields.
header='{
    "typ": "JWT",
    "alg": "RS256"

}'

# Use jq to set the dynamic `iat` and `exp`
# fields on the header using the current time.
# `iat` is set to now, and `exp` is now + 1 second.

payload="{
	\"iat\":$iat,
	\"exp\":$exp,
	\"iss\": \"4f07a93ca98f411f9aa5640268bcf8fc\"
}"

#echo $payload
base64_encode()
{
    declare input=${1:-$(</dev/stdin)}
    # Use `tr` to URL encode the output from base64.
    printf '%s' "${input}" | base64 | tr -d '=' | tr '/+' '_-' | tr -d '\n'
}

json() {
    declare input=${1:-$(</dev/stdin)}
    printf '%s' "${input}" 
}

hmacsha256_sign()
{
    declare input=${1:-$(</dev/stdin)}
    printf '%s' "${input}" | openssl dgst -binary -sha256 -sign "private.key"
}

header_base64=$(echo "${header}"  | base64_encode)
payload_base64=$(echo "${payload}"  | base64_encode)


header_payload=$(echo "${header_base64}.${payload_base64}")
signature=$(echo "${header_payload}" | hmacsha256_sign | base64_encode)

#echo "${header_payload}.${signature}"
slen=${#signature}





t1=${header_payload}.${signature}
# echo ${#t1}
# echo ${#header_payload}
# echo ${#signature}
slen=${#t1}
done


#good
INSTANCE_REGION=$(curl  -H "Content-Type:application/x-www-form-urlencoded; charset=UTF-8" -G --data-urlencode "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer"  --data-urlencode "assertion=${header_payload}.${signature}" -X POST "https://auth.worksmobile.com/b/jp1LXyuohwFqr/server/token" )
totallen=${#INSTANCE_REGION}


firstvalueofquote=$(echo ${INSTANCE_REGION:17:totallen} | grep -b -o '"' | head -1)
lenfirstvalueofquote=${#firstvalueofquote}

#echo $firstvalueofquote
lastposofquote=${firstvalueofquote:0:lenfirstvalueofquote-2} 
#echo $lastposofquote
#echo "${INSTANCE_REGION:17:$lastposofquote}"


url_message="https://apis.worksmobile.com/r/${apiid}/message/v1/bot/${BOTNO}/message/push";
#echo $url_message


# echo "Content-Type:application/x-www-form-urlencoded; charset=UTF-8\n\r Authorization:Bearer ${INSTANCE_REGION}\n\r consumerKey:${CONSUMERKEY} "

# json="{\"roomId\":\"${CHANNELID}\",\"content\":{\"type\":\"text\",\"text\":\"${message_str}\"}}"
# json="{\"accountId\":\"${ACCOUNT_ID}\",\"content\":{\"type\":\"text\",\"text\":\"${message_str}\"}}"



if [ $3 == 0 ]
then
    json="{\"roomId\":\"${ACCOUNT_ID}\",\"content\":{\"type\":\"text\",\"text\":\"${message_str}\"}}"

else
   json="{\"accountId\":\"${ACCOUNT_ID}\",\"content\":{\"type\":\"text\",\"text\":\"${message_str}\"}}"

fi
echo $json

# exit 0


header=$"Content-Type:application/json;charset=UTF-8  Authorization:Bearer ${INSTANCE_REGION:17:$lastposofquote}  consumerKey:${CONSUMERKEY}"

echo $header

# {"accountId":"mehedee@hdn","content":{"type":"text","text":"test test mehedee"}}


response=$(curl -H "Content-Type:application/json; charset=UTF-8" -H "Authorization:Bearer ${INSTANCE_REGION:17:$lastposofquote}" -H "consumerKey:${CONSUMERKEY}"  -d "${json}" -X POST "${url_message}" )
echo $response

















# INSTANCE_REGION=$(curl  -H "Content-Type:application/x-www-form-urlencoded; charset=UTF-8" -G --data-urlencode "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer"  --data-urlencode "assertion=${header_payload}.${signature}" -X POST "https://auth.worksmobile.com/b/jp1LXyuohwFqr/server/token")# --proxy http://10.0.61.216:8081)
# response=$(curl  -H "Content-Type:application/json;charset=UTF-8" -H "Authorization:Bearer ${INSTANCE_REGION:17:$lastposofquote}" -H "consumerKey:${CONSUMERKEY}"  -d "${json}" -X POST "${url_message}") #  --proxy http://10.0.61.216:8081)
# 
