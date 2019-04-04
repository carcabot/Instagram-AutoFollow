# Instagram-AutoFollow

Following easily someone automatically



# How to use

Fill `const sessionId` with your instagram session cookie


```
 $userToFollow = 'rihanna';
 $follow = new InstagramAutoFollow();
 var_dump($follow->doFollow($userToFollow)); // true or false

```

# Requirements

- GuzzleHttp
