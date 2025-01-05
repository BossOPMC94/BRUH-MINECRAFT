FROM ubuntu:22.04
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y openssh-server tmate
RUN mkdir /var/run/sshd
RUN echo 'root:root' | chpasswd
RUN echo 'PermitRootLogin yes' >> /etc/ssh/sshd_config
RUN echo '/usr/bin/tmate' >> /root/.bashrc
EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]
