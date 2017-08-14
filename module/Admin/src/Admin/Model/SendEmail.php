<?php
namespace Admin\Model;

use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

use DOMPDFModule\View\Model\PdfModel;


class SendEmail {

    protected $transport;
    protected $from;
    protected $renderer;
    protected $template;
    
    public function __construct($parent, $template = '')
    {
        $this->transport = $parent->getServiceLocator()->get('mail.transport');
        //$this->renderer = $parent->getServiceLocator()->get('ViewRenderer');
        $this->renderer = $parent->getServiceLocator()->get('Zend\View\Renderer\RendererInterface');
    
        $this->from = 'eaglemay@gmail.com';
        $this->template = $template;
    }
    
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }
    
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }
    
    public function signup($customer)
    {
        $this->setTemplate('admin/email/tpl/signup');
        $subject = 'Signup Confirmation';
    
        $viewContent = new ViewModel(array('customer' => $customer));
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        return $this->send($this->from, $customer->email, $subject, $content);
    }
    
    public function resetpwd($user, $password)
    {
        $this->setTemplate('admin/email/tpl/resetpwd');
        $subject = 'Reset Password Confirmation';
    
        $viewContent = new ViewModel(array('user' => $user, 'password' => $password));
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        return $this->send($this->from, $this->from, $subject, $content);
    }

    public function sendevent($event)
    {
        $this->setTemplate('admin/email/tpl/event');
        $subject = $event->subject;
        
        $viewContent = new ViewModel(array('event' => $event));
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        return $this->send($this->from, $this->from, $subject, $content);
    }

    public function sendnotice($notice)
    {
        $this->setTemplate('admin/email/tpl/notice');
        $subject = $notice->subject;
    
        $viewContent = new ViewModel(array('notice' => $notice));
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        return $this->send($this->from, $this->from, $subject, $content);
    }

    public function sendrequest($request)
    {
        $this->setTemplate('admin/email/tpl/request');
        $subject = $request->subject;
    
        $viewContent = new ViewModel(array('request' => $request));
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        return $this->send($this->from, $this->from, $subject, $content);
    }
    
    public function sendresponse($response)
    {
        $this->setTemplate('admin/email/tpl/response');
        $subject = $response->subject;
    
        $viewContent = new ViewModel(array('notice' => $response));
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        return $this->send($this->from, $this->from, $subject, $content);
    }
    
    public function sendRecipt()
    {
        $this->setTemplate('admin/email/tpl/receipt');
        $subject = 'Receipt';
        
        //$viewContent = new ViewModel(array('customer' => $customer));
        $viewContent = new ViewModel();
        $viewContent->setTemplate($this->template);
        $content = $this->renderer->render($viewContent);
        
        $attachments = [];
        $attachments[0] = $this->createReceipt();
        
        //return $this->send($this->from, $customer->email, $subject, $content, $attachment);
        return $this->send($this->from, 'eaglemay@gmail.com', $subject, $content, $attachments);
    }
        
    public function createReceipt()
    { 
        $filename =  'recipt';
        
        $pdf = new PdfModel();
        $pdf->setTemplate('admin/email/tpl/receipt');
        
        $pdf->setOption('filename', $filename);
        
        $pdf->setVariables(array(
            'message' => 'Hello'
        ));
        
        $filename = $filename . '.pdf';
        
        $fileContent = $this->renderer->render($pdf);
        $attachment = new MimePart($fileContent);
        $attachment->type = 'application/pdf';
        $attachment->filename = $filename;
        $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
        $attachment->encoding = Mime::ENCODING_BASE64;
        
        return $attachment;
    }

    public function createAttachment($filename)
    {
        $path_parts = pathinfo($filename);
        
        $fileContent = fopen($filename, 'r');
        $attachment = new MimePart($fileContent);
        $attachment->type = Mime::TYPE_OCTETSTREAM;
        $attachment->filename = $path_parts['filename'];
        $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
        $attachment->encoding = Mime::ENCODING_BASE64;
    
        return $attachment;
    }
    /*
    protected function send($from, $to, $subject, $content) {
    
        if($from == '') {
            $from = $this->from;
        }
    
        $message = new Message();
        $message->addFrom($this->from)
                ->addTo($to)
                ->setSubject($subject);
            
        $viewLayout = new ViewModel(array('content' => $content));
        $viewLayout->setTemplate('admin/email/tpl/layout.phtml');
        $content = $this->renderer->render($viewLayout);
    
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html,));
    
        $message->setBody($body);
    
        $this->transport->send($message);
    }
    */
    protected function send($from, $to, $subject, $content, $attachments = null) {
        
        if($from == '') {
            $from = $this->from;
        }
        
        $message = new Message();
        $message->addFrom($this->from)
        ->addTo($to)
        ->setSubject($subject);
    
        $viewLayout = new ViewModel(array('content' => $content));
        $viewLayout->setTemplate('admin/email/tpl/layout');
        $content = $this->renderer->render($viewLayout);
        
        $html = new MimePart($content);
        $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $html->type     = "text/html; charset=UTF-8";
        
        $body = new MimeMessage();
        
        if ($attachments) {
            
            $content2 = new MimeMessage();
            $content2->addPart($html);
    
            $contentPart = new MimePart($content2->generateMessage());
            $contentPart->type = "multipart/alternative;\n boundary=\"" .
                $content2->getMime()->boundary() . '"';
    
            $body->addPart($contentPart);
            $messageType = 'multipart/related';
    
            // Add each attachment
            foreach ($attachments as $attachment) {
                /*
                $attachment = new MimePart($thisAttachment['content']);
                $attachment->filename    = $thisAttachment['filename'];
                $attachment->type        = Mime::TYPE_OCTETSTREAM;
                $attachment->encoding    = Mime::ENCODING_BASE64;
                $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                */
                $body->addPart($attachment);
            }
            $message->setBody($body);
            $message->getHeaders()->get('content-type')->setType($messageType);
            
        } else {
            $body->setParts(array($html,));
            //$messageType = 'multipart/alternative';
            $message->setBody($body);
        }
    
        $message->setEncoding('UTF-8');
        $this->transport->send($message);
    }
    

}