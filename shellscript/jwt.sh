
iat=$(date +%s)
exp=$(date --date="+3600 seconds" '+%s')


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
	\"iss\": \"\"
}"

echo $payload
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
    printf '%s' "${input}" | openssl dgst -binary -sha256 -sign "private_20210927160313.key"
}

header_base64=$(echo "${header}"  | base64_encode)
payload_base64=$(echo "${payload}"  | base64_encode)

header_payload=$(echo "${header_base64}.${payload_base64}")
signature=$(echo "${header_payload}" | hmacsha256_sign | base64_encode)

echo "${header_payload}.${signature}"
